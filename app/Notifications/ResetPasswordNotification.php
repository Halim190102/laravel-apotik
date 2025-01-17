<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected $code)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset Code')
            ->greeting("Hello!, {$notifiable->name}")
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Your password reset code is:')
            ->line(new HtmlString('<div style="text-align: center; font-weight: bold; font-size: 32px; color: #000; letter-spacing: 10px; padding: 20px 0;">' . $this->code . '</div>'))
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Regards, The Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
