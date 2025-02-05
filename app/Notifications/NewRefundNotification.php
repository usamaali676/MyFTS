<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRefundNotification extends Notification
{
    use Queueable;

    private $title;
    private $refund;
    private $invoice;



    /**
     * Create a new notification instance.
     */
    public function __construct( $refund, $title, $invoice)
    {
        $this->refund = $refund;
        $this->title = $title;
        $this->invoice = $invoice;
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
        // dd($this->invoice);
        return [
            'title' => $this->title,
            'refund' => $this->refund->id,
            'invoice_number' => $this->invoice->invoice_number,
            'added_by' => $this->invoice->created_by,
        ];
    }
}
