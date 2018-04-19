<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ThreadReceivedNewReply;
use App\Listeners\NotifyMentionedUsers;
use App\Listeners\NotifySubscribers;
use App\Events\ThreadWasPublished;

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
