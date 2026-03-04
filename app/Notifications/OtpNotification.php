<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;

class OtpNotification extends Notification
{
    use Queueable;
    private $user;


    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        // dd($this->user);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
                    ->text( $this->user->name . ' OTP is: ' . $this->user->otp);
    }
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

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
