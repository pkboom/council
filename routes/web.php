<?php


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'ThreadController@index')->name('threads');
Route::get('/threads', 'ThreadController@index')->name('threads');
Route::post('/threads', 'ThreadController@store')->middleware('must-be-confirmed');
Route::get('/threads/create', 'ThreadController@create')->middleware('must-be-confirmed');
Route::get('/threads/search', 'SearchController@show');
Route::get('/threads/{channel}', 'ThreadController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show');
Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy');
Route::patch('/threads/{channel}/{thread}', 'ThreadController@update');
Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index');
Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store')->middleware('auth');
Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')->middleware('auth');

Route::patch('/replies/{reply}', 'ReplyController@update')->middleware('auth');
Route::delete('/replies/{reply}', 'ReplyController@destroy')->middleware('auth')->name('replies.destory');
Route::post('/replies/{reply}/favorites', 'FavoriteController@store')->middleware('auth');
Route::delete('/replies/{reply}/favorites', 'FavoriteController@destroy')->middleware('auth');
Route::post('/replies/{reply}/best', 'BestReplyController@store')->middleware('auth')->name('best-replies.store');

Route::get('/profiles/{user}', 'ProfileController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationController@index')->middleware('auth');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationController@destroy')->middleware('auth');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');
Route::get('/api/users', 'Api\UserController@index');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');

Route::post('locked-threads/{thread}', 'LockedThreadController@store')->middleware('admin')->name('locked-threads.store');
Route::delete('locked-threads/{thread}', 'LockedThreadController@destory')->middleware('admin')->name('locked-threads.destory');
