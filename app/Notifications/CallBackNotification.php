<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;

class CallBackNotification extends Notification
{
    use Queueable;
    private $users;
    public $lead;

    /**
     * Create a new notification instance.
     */
    public function __construct($users, $lead)
    {
        $this->users = $users;
        $this->lead = $lead;
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
     * Get the mail representation of the notification.
     */
        public function toSlack(object $notifiable): SlackMessage
    {

       foreach ($this->users as $user) {
            return (new SlackMessage)
                ->text('Lead: ' . $this->lead->first()->business_name_adv . ' is scheduled for a callback today. Assigned to: ' . $user->name);
        }
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
