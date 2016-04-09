<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['web']], function () {

Route::auth();

Route::get('/', function() {
    return view('pages.index');
});

Route::resource('/chat', 'ChatsController');

Route::get('fire', function () {
    // this fires the event
    event(new App\Events\ChatMessages());
    return "event fired";
});

Route::get('test', function () {
    // this checks for the event
    return view('chat/test');
});


Route::group(['middleware' => 'auth'], function () {

Route::get('/chats', 'ChatsController@index');
Route::post('/chats/new/{user_id}', 'ChatsController@createChat');
Route::post('/chats/current/{user_id}', 'ChatsController@getChats');
Route::post('/chats/message/{user_id}', 'ChatsController@store');
//Route::get('/chats/users', 'ChatsController@getUsers');
Route::get('users/getAll', 'ChatsController@getAllUsers');

});


});
