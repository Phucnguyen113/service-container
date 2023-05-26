<?php

use Phucrr\Php\Support\Facades\Route;

Route::get('/', function () {
    return '123';
});
Route::get('/', 'abc@123');