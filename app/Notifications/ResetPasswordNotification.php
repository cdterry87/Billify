<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * The branded password reset email, sent through the queue so the forgot-password page
 * answers just as fast whether or not the account exists.
 */
class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * @param  User  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $data = [
            'name' => $notifiable->name,
            'url' => $this->resetUrl($notifiable),
            'expires' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
        ];

        return (new MailMessage)
            ->subject('Reset your '.config('app.name').' password')
            ->view(['mail.reset-password', 'mail.reset-password-text'], $data);
    }
}
