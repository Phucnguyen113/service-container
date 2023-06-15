<?php

use Phucrr\Php\Support\Facades\Route;

Route::get('/oke/{id}/alo/{as}', function () {
    return '123';
});

Route::group(['prefix' => 'group'], function () {
    Route::get('zxc', 'grouxyz@in');
    Route::post('store', 'asdq@iqwe');
    Route::group(['prefix' => 'group2222', 'namespace' => 'hellowrod'], function ($route) {
        $route->get('asd', 'qwe@qwe');
        $route->post('tyu', 'tyu@tyu');
    });
    Route::get('afterGroup', 'gg@i');
});