<?php

use App\Http\Controllers\StreamingChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/chat/streaming", [StreamingChatController::class, 'index']);
