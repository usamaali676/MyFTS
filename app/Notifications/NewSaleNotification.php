<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSaleNotification extends Notification
{
    use Queueable;
    protected $sale;
    protected $lead;
    protected $title;


    /**
     * Create a new notification instance.
     */
    public function __construct($sale,  $lead, $title,)
    {
        $this->sale = $sale;
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


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // dd($this->lead);
        return [
            'title' => $this->title,
            'sale_id' => $this->sale->id,
            'lead_name' => $this->lead->business_name_adv,
            'added_by' => $this->sale->created_by,
        ];
    }
}
