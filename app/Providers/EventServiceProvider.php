<?php

namespace App\Providers;

use App\Events\ThreadWasPublished;
use App\Listeners\NotifySubscribers;
use Illuminate\Support\Facades\Event;
use App\Events\ThreadReceivedNewReply;
use App\Listeners\NotifyMentionedUsers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ThreadReceivedNewReply::class => [
            NotifyMentionedUsers::class,
            NotifySubscribers::class
        ],
        ThreadWasPublished::class => [
            NotifyMentionedUsers::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
