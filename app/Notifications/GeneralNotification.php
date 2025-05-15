<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $title;
    protected $content;

    protected $via;
    protected $type;
    public function __construct($t, $c, $v = ['database'], $type = 'general')
    {
        $this->title = $t;
        $this->content = $c;

        $this->via = $v;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->via;
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
            'content' => $this->content,
            'type' => $this->type,
        ];
    }
}
