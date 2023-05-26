<?php
namespace Phucrr\Php\Providers;

use Phucrr\Php\Route\Router;
use Phucrr\Php\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('route', function () {
            return new Router;
        });
    }
}