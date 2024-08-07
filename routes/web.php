<?php

use App\Http\Controllers\UrlShortenerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['controller' => UrlShortenerController::class], function () {
    Route::get('encode', 'encode')->name('encode');
    Route::get('decode', 'decode')->name('decode');
});
