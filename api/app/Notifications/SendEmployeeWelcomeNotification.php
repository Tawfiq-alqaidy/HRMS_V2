<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendEmployeeWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $plainPassword;
    public $email;

    public function __construct($plainPassword, $email)
    {
        $this->plainPassword = $plainPassword;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to HRMS - Your Account Details')
            ->greeting('Welcome!')
            ->line('Your account has been created. Please use the following credentials to log in:')
            ->line('Email: ' . $this->email)
            ->line('Password: ' . $this->plainPassword)
            ->line('For security, please change your password after your first login.')
            ->line('If you did not expect this email, please contact HR immediately.');
    }
}
