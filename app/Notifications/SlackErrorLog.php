<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SlackErrorLog extends Notification implements ShouldQueue
{
    use Queueable;
    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
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

    public function toSlack(object $notifiable): SlackMessage
    {
        $data = $this->data;

        return (new SlackMessage)
            ->error()
            ->content($data['process'])
            ->attachment(function (SlackAttachment $att) use ($data, $notifiable) {
                $att->title('Error Details')
                    ->fields(array_merge([
                        'user' => (array_key_exists('email', (array) $notifiable)) ? ($notifiable->email . ' | ' . $notifiable->name) : 'System Error',
                        'url' => $data['url'],

                    ], $data['th']));
            });
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
