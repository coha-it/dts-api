<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailCtrl extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'auth.user.email']);
    }

    /**
     * Send Contact-Mail for Pan-User
     *
     * @return void
     */
    public function sendEntranceMail(Request $request) {

        // Validate Data
        $request->validate([
            'id' => 'required'
        ]);

        // Get Created User with Relation
        $panUser = $request->user()->users->find($request->id)->getSelfWithRelations($request->id);
        $email = $panUser->pan->contact_mail;

        // Send Mail
        Mail::send(
            'emails.entrance',
            [
                'user'      => $panUser,
                'signature' => $request->entrance['signature'] ?? '',
                'text'      => $request->entrance['text'] ?? '',
            ],
            function ($mail) use ($request, $email) {
                $mail
                    ->from('it@corporate-happiness.de', env('APP_NAME'))
                    ->to($email)
                    ->subject(
                        $request->entrance['subject'] ?? __('Welcome - Your Entrance-Information')
                    );
            }
        );

        $response = '';
        $response .= ($this->correct_format($email)) ? null: "Wrong Format! \n";
        $response .= ($this->domain_exists($email)) ? null: "No DNS! \n";

        if( count(Mail::failures()) > 0 ) {

            $response .= "There was one or more failures. They were: \n";

            foreach(Mail::failures() as $email_address) {
                $response .= " - $email_address \n";
             }

         } else {
             $response .= "Success: Sending Mail \n";
         }

        // Update User with sending Mail
        $panUser->pan['last_mail_status'] = $response;
        $panUser->pan['last_mail_date'] = now()->toDateTimeString();

        // Save PAN
        $panUser->pan->save();
    }

    // Check E-Mail Format
    public function correct_format($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // An optional sender
    public function domain_exists($email, $record = 'MX'){
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, $record);
    }

}
