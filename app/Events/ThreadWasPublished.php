<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ThreadWasPublished
{
    use SerializesModels;

    public $thread;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    public function subject()
    {
        return $this->thread;
    }
}
