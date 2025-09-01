<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CensusCreatedNotification extends Notification
{
    use Queueable;

    protected $census;
    protected $message;

    public function __construct($census, $message)
    {
        $this->census = $census;
        $this->message = $message;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => route('census.show', ['census' => $this->census->id]),
        ];
    }
}
