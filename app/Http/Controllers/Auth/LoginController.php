<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\VerifyEmailException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use Mail;
use App\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if (! $token) {
            return false;
        }

        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        $this->guard()->setToken($token);

        return true;
    }

    /**
     * Attempt to log the user into the application via his PAN
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLoginPan(Request $req)
    {
        // Validate Request
        $this->validate(
            $req,
            [
                'pan' => 'required',
                'pin' => 'required',
            ],
            [
                'required' => 'schlamm'
            ]
        );

        // Transform PAN
        $pan = str_replace(' ', '', $req->pan); // Remove Whitespace
        $pin = $req->pin;
        $now = Carbon::now();

        // 1. Check PAN! Try to Find the User
        $user = User::whereHas('pan', function ($query) use ($pan) {
            $query->where('pan', '=', $pan);
        })->first();

        if(!$user) {
            // Found no User
            return $this->loginPanErrorResponse(
                "PAN & PIN Combination failed",
                [
                    "pan" => ["auth.failed"],
                    "pin" => ["auth.failed"]
                ]
            );
        } else {
            $upan = $user->pan;
        }

        // 2. Check dates "locked_until" | If Account is still Locked for XX Hours
        if($this->accountIsAlreadyLocked($upan, $now))
        {
            return $this->sendLockedResponse($upan, $now);
        }

        // 3. Check "Failed Login Attempts"!
        if ($this->tooManyFailedLogins($upan))
        {
            $this->lockAccount($upan, 24, $req->ip());
            return $this->sendLockedResponse($upan, $now);
        }

        // 4. Check PAN && PIN
        if ($this->checkPanAndPin($upan, $pan, $pin)) {
            // Login Success
            Auth::login($user);

            // Reset Failed Logins
            $upan->failed_logins = 0;
            $upan->save();

            // Send PAN Login Credentials
            $token = (string) $this->guard()->getToken();
            $expiration = $this->guard()->getPayload()->get('exp');

            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expiration - time(),
            ]);
        } else {
            // Login Failed
            $upan->failed_logins += 1;
            $upan->save();

            return $this->loginPanErrorResponse(
                "PAN & PIN Combination failed",
                [
                    "pan" => ["auth.failed"],
                    "pin" => ["auth.failed"]
                ]
            );
        }
    }

    /**
     * Check if the requested PAN and PIN are correct
     *
     * @return Boolean
     */
    protected function checkPanAndPin($u, $pan, $pin) {
        return
                $u->pan === $pan &&
                $u->pin === $pin;
    }

    /**
     * Check if the Account is already locked
     *
     * @return Boolean
     */
    protected function accountIsAlreadyLocked($u, $now) {
        return
                $u->locked_until &&
                $u->locked_until >= $now;
    }

    /**
     * Check if to many failed logins were made
     * If more than XX, return true
     *
     * @return Boolean
     */
    protected function tooManyFailedLogins($u) {
        return
            $u->failed_logins &&
            $u->failed_logins >= 7;
    }

    /**
     * Lock the Users Account
     *
     * @param \App\User $user
     */
    protected function lockAccount($upan, $hours, $ip = "") {
        // Add XX Hours and lock user, but reset failed attempts to 0
        // Lock Account
        $upan->locked_until = Carbon::now()->copy()->addHours($hours);
        $upan->failed_logins = 0;
        $upan->save();

        // Send Mail
        $msg = '';
        $msg .= "Account ID: \"".$upan->user()->first()->id."\" locked! \r\n";
        $msg .= "Login try from IP:\"$ip\" \r\n";

        Mail::raw($msg, function($message)
        {
            $message->from('it@corporate-happiness.de', env('APP_NAME'));
            $message->subject('Account Locked');
            $message->to('it@corporate-happiness.de');
        });
    }

    /**
     * Send the User is locked Response
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLockedResponse($user, $now) {
        // Display time to wait. Should be XX (~24) Hours
        $diff = $now->diffInHours($user->locked_until);
        return $this->loginPanErrorResponse(
            __("auth.acc_locked", ['hours' => $diff]), [], $status=423
        );
    }

    /**
     * Return the Error-Response for failed Pan-Login
     *
     * @param  string $message
     * @param  array  $errors
     * @param  int    $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginPanErrorResponse($message, $errors = [], $status = 400) {
        return response()->json([
            "message" => $message,
            "errors" => $errors
        ], $status);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            throw VerifyEmailException::forUser($user);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
    }
}
