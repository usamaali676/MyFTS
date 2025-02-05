<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $lead;
    protected $title;
    protected $link;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $lead, $title)
    {
        $this->comment = $comment;
        $this->lead = $lead;
        $this->title = $title;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            
            'title' => $this->title,
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->business_name_adv,
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'added_by' => $this->comment->user_id, // Assuming comments have a user relationship
        ];
    }
}
