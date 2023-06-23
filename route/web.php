<?php

use Phucrr\Php\Support\Facades\Route;

Route::get('products/{id}/edit/{a}', 'ProductController@index');
Route::get('view', function () {
    return view('test', ['a' => '<script>alert("aaaa")</script>', 'b' => 'number b', 'c' => '<span style="color:red">c~~~</span>']);
});
Route::group(['prefix' => 'group'], function () {
    Route::get('zxc', 'grouxyz@in');
    Route::post('store', 'asdq@iqwe');
    Route::group(['prefix' => 'group2222', 'namespace' => 'Users'], function ($route) {
        $route->get('user', 'UserController@index');
        $route->post('tyu', 'tyu@tyu');
    });
    Route::get('afterGroup', 'gg@i');
});