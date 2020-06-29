<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserNewsletter as Newsletter;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validate All Requests
        $this->validate($request, [
            'email_verified_at' => 'max:0',
            'right' => 'max:0',
        ]);

        if ($user->isEmailUser()) {
            // If User is E-Mail User
            $this->validate($request, [
                'email' => 'required|email|unique:users,email,'.$user->id,
            ]);

            // Newsletter
            if($request->newsletter === false) {
                $user->newsletter()->delete();
            } else if($request->newsletter === true) {
                $user->newsletter()->updateOrCreate([
                    'user_id' => $request->user()->id
                ]);
            }

        } else {
            // Is PAN-User
            $this->validate($request, [
                'pin' => 'min:4',
                'new_pin' => 'min:4',
                'email' => 'present|max:0'
            ]);

            // If new Pin
            if (
                $request['new_pin']
            ) {

                if ($request['pin'] === $user->pan->pin) {
                    $user->pan->pin = $request['new_pin'];
                    $user->pan->save();
                } else {
                    return response()->json("Wrong Pin", 405);
                }
            }

            // Change PIN
        }

        // Update!
        $user->update([
            "email" => $request['email'] ?? null,
            "department_id" => $request['department_id'] ?? null,
            "company_id" => $request['company_id'] ?? null,
            "location_id" => $request['location_id'] ?? null,
        ]);

        // Return User!
        return $user->getSelfWithRelations();
    }
}
