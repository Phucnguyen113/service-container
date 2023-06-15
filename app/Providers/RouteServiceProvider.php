<?php
namespace App\Providers;

use Phucrr\Php\Support\Facades\Route;
use Phucrr\Php\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

    public function register()
    {
        Route::middleware('web')->group(dirname(__DIR__).'../../route/web.php');
    }
}