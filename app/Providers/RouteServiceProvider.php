<?php
namespace App\Providers;

use Phucrr\Php\Support\Facades\Route;
use Phucrr\Php\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

    public $namespace = 'App\Http\Controllers';

    public function register()
    {
        Route::middleware('web')->namespace($this->namespace)->group(dirname(__DIR__).'../../route/web.php');
    }
}