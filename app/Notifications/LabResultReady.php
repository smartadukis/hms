<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LabResultReady extends Notification
{
    use Queueable;

    public $labTest;

    /**
     * Create a new notification instance.
     */
    public function __construct(LabTest $labTest)
    {
        $this->labTest = $labTest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
   

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
            'message' => 'Lab result for ' . $this->labTest->patient->name . ' is ready.',
            'link' => route('lab-tests.edit', $this->labTest->id),
        ];
    }
}
