<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/operator/chat', [App\Http\Controllers\HomeController::class, 'chat'])->name('operator.chat');
Route::get('/operator/users', [App\Http\Controllers\HomeController::class, 'users'])->name('operator.users');
Route::get('/operator/messages', [App\Http\Controllers\HomeController::class, 'messages'])->name('operator.messages');
Route::post('/operator/messages', [App\Http\Controllers\HomeController::class, 'messageStore'])->name('operator.message-store');
Route::get('/operator/contact-messages/{id}', [App\Http\Controllers\HomeController::class, 'contactMessages'])->name('operator.contact-messages');
Route::get('/operator/read/{recevier}', [App\Http\Controllers\HomeController::class, 'read'])->name('operator.read');
Route::get('/operator/user/fetch-unread/{sender}', [App\Http\Controllers\HomeController::class, 'fetchUnread'])->name('operator.fetch.unread');
