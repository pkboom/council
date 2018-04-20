<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ThreadReceivedNewReply
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $reply;

    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    public function subject()
    {
        return $this->reply;
    }
}
