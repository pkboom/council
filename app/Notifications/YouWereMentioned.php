<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Reply;

class YouWereMentioned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
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

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message(),
            'notifier' => $this->user(),
            'link' => $this->subject->path()
        ];
    }

    public function message()
    {
        return sprintf('%s mentioned you in "%s"', $this->user()->username, $this->subject->title());
    }

    public function user()
    {
        return $this->subject instanceof Reply ? $this->subject->owner : $this->subject->creator;
    }
}
