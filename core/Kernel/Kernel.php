<?php
namespace Phucrr\Php\Kernel;

use Phucrr\Php\Application;
use Phucrr\Php\Contracts\RequestContract;
use Phucrr\Php\Contracts\Router;

abstract class Kernel {
    
    public $app;
    public $router;
    public $bootstraps = [
        \Phucrr\Php\Bootstraps\RegisterFacade::class,
        \Phucrr\Php\Bootstraps\RegisterProviders::class,
    ];

    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    public function bootstrap()
    {
        $this->app->bootstrapWith($this->bootstraps);
    }

    public function handle(RequestContract $request)
    {
        $this->bootstrap();
        $this->dispatchToRouter($request);
    }

    public function dispatchToRouter(RequestContract $request)
    {
        $this->router->setRequest($request)->dispatch();
    }
}