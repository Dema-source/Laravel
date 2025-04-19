<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/home', function () {
    return view('welcome');
});

Route::get('/content/{id}', [BookController::class, 'getContent']);
