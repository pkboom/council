<?php

use App\Jobs\PerformLongRunningThing;

Route::get('some', function () {
    // PerformLongRunningThing::dispatch('Now')->delay(now()->addMinutes(3));
    dispatch(new PerformLongRunningThing('now1'))->delay(now()->addMinute());
});

Route::redirect('/', 'threads');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/threads', 'ThreadController@index')->name('threads');
Route::post('/threads', 'ThreadController@store')->middleware('must-be-confirmed')->name('threads.store');
Route::get('/threads/channels', 'ChannelController@show')->name('channels.show');
Route::get('/threads/create', 'ThreadController@create')->middleware('must-be-confirmed')->name('threads.create');
Route::get('/threads/search', 'SearchController@show')->name('search.show');
Route::get('/threads/{channel}', 'ThreadController@index')->name('channels');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show')->name('threads.show');
Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy')->name('threads.destroy');
Route::patch('/threads/{channel}/{thread}', 'ThreadController@update')->name('threads.update');
Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index')->name('replies');
Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store')->middleware('auth', 'must-be-confirmed')->name('replies.store');
Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@store')->middleware('auth')->name('subscriptions.store');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')->middleware('auth')->name('subscriptions.destroy');

Route::patch('/replies/{reply}', 'ReplyController@update')->middleware('auth')->name('replies.update');
Route::delete('/replies/{reply}', 'ReplyController@destroy')->middleware('auth')->name('replies.destory');
Route::post('/replies/{reply}/favorites', 'FavoriteController@store')->middleware('auth')->name('replies.favorite');
Route::delete('/replies/{reply}/favorites', 'FavoriteController@destroy')->middleware('auth')->name('replies.unfavorite');
Route::post('/replies/{reply}/best', 'BestReplyController@store')->middleware('auth')->name('best-replies.store');

Route::get('/profiles/{user}', 'ProfileController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationController@index')->middleware('auth')->name('user-notifications');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationController@destroy')->middleware('auth')->name('user-notification.destroy');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');
Route::get('/api/users', 'Api\UserController@index')->name('api.users');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');

Route::post('locked-threads/{thread}', 'LockedThreadController@store')->middleware('admin')->name('locked-threads.store');
Route::delete('locked-threads/{thread}', 'LockedThreadController@destory')->middleware('admin')->name('locked-threads.destory');

Route::post('pinned-threads/{thread}', 'PinnedThreadController@store')->middleware('admin')->name('pinned-threads.store');
Route::delete('pinned-threads/{thread}', 'PinnedThreadController@destroy')->middleware('admin')->name('pinned-threads.destory');

Route::namespace('Admin')->prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', 'DashboardController@index')->name('admin.dashboard.index');
    Route::get('/channels', 'ChannelController@index')->name('admin.channels.index');
    Route::post('/channels', 'ChannelController@store')->name('admin.channels.store');
    Route::get('/channels/create', 'ChannelController@create')->name('admin.channels.create');
    Route::get('/channels/{channel}/edit', 'ChannelController@edit')->name('admin.channels.edit');
    Route::patch('/channels/{channel}/edit', 'ChannelController@update')->name('admin.channels.update');
});
