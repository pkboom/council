<?php

use Ramsey\Uuid\Uuid;
use Faker\Generator as Faker;
use const App\Notifications\ThreadWasUpdated;
use Illuminate\Notifications\DatabaseNotification;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'username' => $faker->unique()->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'confirmed' => true,
        'remember_token' => str_random(10),
    ];
});

$factory->state(App\User::class, 'unconfirmed', [
    'confirmed' => false,
]);

$factory->define(App\Thread::class, function (Faker $faker) {
    $title = $faker->sentence;

    return [
        'user_id' => factory(App\User::class),
        'channel_id' => factory(App\Channel::class),
        'title' => $title,
        'body' => $faker->paragraph,
        'visits' => 0,
        'slug' => str_slug($title),
        'locked' => false
    ];
});

$factory->state(App\Thread::class, 'from_existing_channels_and_users', function ($faker) {
    $title = $faker->sentence;

    return [
        'user_id' => factory(App\User::class),
        'channel_id' => factory(App\Channel::class),
        'title' => $title,
        'body' => $faker->paragraph,
        'visits' => $faker->numberBetween(0, 35),
        'slug' => str_slug($title),
        'locked' => $faker->boolean(15)
    ];
});

$factory->define(App\Reply::class, function (Faker $faker) {
    return [
        'thread_id' => factory(App\Thread::class),
        'user_id' => factory(App\User::class),
        'body' => $faker->paragraph
    ];
});

$factory->define(DatabaseNotification::class, function ($faker) {
    return [
        'id' => Uuid::uuid4()->toString(),
        'type' => ThreadWasUpdated::class,
        'notifiable_id' => function () {
            return auth()->id() ?? factory(App\User::class);
        },
        'notifiable_type' => App\User::class,
        'data' => ['foo' => 'bar'],
    ];
});
