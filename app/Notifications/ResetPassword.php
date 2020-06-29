<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class ResetPassword extends Notification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line(__('mail.password.received'))
            ->action(__('mail.password.reset'), url(config('app.url').'/password/reset/'.$this->token).'?email='.urlencode($notifiable->email))
            ->line(__('mail.password.no-action'));
    }
}
