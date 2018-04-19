<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ThreadWasUpdated extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $thread;

    protected $reply;

    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
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
            'message' => $this->reply->owner->name . ' replied to ' . $this->thread->title,
            'notifier' => $this->reply->owner,
            'link' => $this->reply->path()
        ];
    }
}
