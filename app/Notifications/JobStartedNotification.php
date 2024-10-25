<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobStartedNotification extends Notification
{
    use Queueable;

    protected $jobName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobName)
    {
        $this->jobName = $jobName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => "El job {$this->jobName} ha comenzado.",
        ];
    }
}