<?php

use Phucrr\Php\Support\Facades\Route;
use Phucrr\Php\Support\Request;

Route::get('products/{id}/edit/{a}', 'ProductController@index');

Route::group(['prefix' => 'group'], function () {
    Route::get('zxc', 'grouxyz@in');
    Route::post('store', 'asdq@iqwe');
    Route::group(['prefix' => 'group2222', 'namespace' => 'Users'], function ($route) {
        $route->get('user', 'UserController@index');
        $route->post('tyu', 'tyu@tyu');
    });
    Route::get('afterGroup', 'gg@i');
});