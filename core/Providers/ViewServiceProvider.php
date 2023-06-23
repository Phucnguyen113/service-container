<?php
namespace Phucrr\Php\Providers;

use Phucrr\Php\Support\ServiceProvider;
use Phucrr\Php\Support\View;

class ViewServiceProvider extends ServiceProvider {

    public function register()
    {   
        $app = $this->app;
        $this->app->singleton('view', function () use ($app){
            return new View($app->path(), $app->resourcePath());
        });
    }
}