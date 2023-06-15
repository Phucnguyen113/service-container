<?php
namespace Phucrr\Php\Support;

use Phucrr\Php\Application;

abstract class ServiceProvider {
    public $defer = false;
    public $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register()
    {

    }

}