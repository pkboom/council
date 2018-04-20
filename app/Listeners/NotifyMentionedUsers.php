<?php

namespace App\Listeners;

use App\User;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        tap($event->subject(), function ($subject) {
            User::whereIn('username', $this->mentionedUsers($subject->body))
            ->get()->each->notify(new YouWereMentioned($subject));
        });
    }

    public function mentionedUsers($body)
    {
        preg_match_all('/@([\w\-]+)/', $body, $matches);

        return $matches[1];
    }
}
