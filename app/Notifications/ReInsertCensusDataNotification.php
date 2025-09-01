<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReInsertCensusDataNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $model;
    protected $message;
    protected $status;

    public function __construct($model, $message, $status = true)
    {
        $this->model = $model;
        $this->message = $message;
        $this->status = $status;
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
        if ($this->status) {
            return [
                'message' => $this->message,
                'url' => route('doctors.show', ['doctor' => $this->model->id]),
            ];
        } else {

            return [
                'message' => $this->message,
                'url' => route('incidences.show', ['incidence' => $this->model->id]),
            ];
        }
    }
}
