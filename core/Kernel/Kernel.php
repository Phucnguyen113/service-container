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
    /**
     * Bootstrap the application
     * 
     * @return null
     */
    public function bootstrap()
    {
        $this->app->bootstrapWith($this->bootstraps);
    }

    /**
     * Handle the request
     * 
     * @param RequestContract $request
     * 
     */
    public function handle(RequestContract $request)
    {
        $this->bootstrap();
        return $this->dispatchToRouter($request);
    }

    public function dispatchToRouter(RequestContract $request)
    {
        return $this->router->setRequest($request)->dispatch();
    }
}