<?php

//Route::get('/', function () {
//    return view('welcome');
//});

Route::redirect('/', 'threads');

Auth::routes();

// Route::view('/scan', 'scan');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index')->name('threads');
Route::post('/threads', 'ThreadsController@store')->middleware('must-be-confirmed')->name('threads.store');
Route::get('/threads/create', 'ThreadsController@create')->middleware('must-be-confirmed')->name('threads.create');
Route::get('/threads/search', 'SearchController@show')->name('search.show');
Route::get('/threads/{channel}', 'ThreadsController@index')->name('channels');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update')->name('threads.update');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy')->name('threads.destroy');

Route::post('/locked-threads/{thread}', 'LockThreadsController@store')->middleware('admin')->name('locked-threads.store');
Route::delete('/locked-threads/{thread}', 'LockThreadsController@destroy')->middleware('admin')->name('locked-threads.destroy');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@store')->middleware('auth')->name('threads-subscription.store');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')->middleware('auth')->name('threads-subscription.destroy');

Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index')->name('replies');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store')->name('replies.store');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');
Route::patch('/replies/{reply}', 'RepliesController@update')->name('replies.update');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store')->name('replies.favorite');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy')->name('replies.unfavorite');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');

Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index')->name('user-notifications');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy')->name('user-notifications.destroy');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');

Route::get('/api/users', 'Api\UsersController@index')->name('api.users');
Route::post('/api/users/{user}/avatars', 'Api\UserAvatarController@store')->middleware('auth')->name('avatars');
Route::get('/api/channels', 'Api\ChannelsController@index');

Route::post('/pinned-threads/{thread}', 'PinnedThreadsController@store')->middleware('admin')->name('pinned-threads.store');
Route::delete('/pinned-threads/{thread}', 'PinnedThreadsController@destroy')->middleware('admin')->name('pinned-threads.destroy');

Route::group([
    'prefix' => 'admin',
    'middleware' => 'admin',
    'namespace' => 'Admin',
], function () {
    Route::get('/', 'DashboardController@index')->name('admin.dashboard.index');
    Route::get('/channels', 'ChannelsController@index')->name('admin.channels.index');
    Route::post('/channels', 'ChannelsController@store')->name('admin.channels.store');
    Route::get('/channels/create', 'ChannelsController@create')->name('admin.channels.create');
    Route::get('/channels/{channel}/edit', 'ChannelsController@edit')->name('admin.channels.edit');
    Route::patch('/channels/{channel}', 'ChannelsController@update')->name('admin.channels.update');
});
