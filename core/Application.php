<?php
namespace Phucrr\Php;

use Phucrr\Php\Container;
use Phucrr\Php\Contracts\Router as ContractsRouter;
use Phucrr\Php\Providers\RoutingServiceProvider;
use Phucrr\Php\Route\Router;

class Application extends Container{

    public $serviceProviders = [];
    public $loadedProviders = [];

    public function __construct()
    {
        static::$instance = $this;
        $this->bindingAliases();
        $this->bindingBasicProvider();
        $this->bindingBasic();
    }

    private function bindingBasicProvider()
    {
        $this->register(RoutingServiceProvider::class);
    }


    private function bindingBasic()
    {
        $this->singleton(Application::class, function () {
            return $this;
        });
    }

    private function bindingAliases()
    {
        $aliases = [
            'route' => [Router::class, ContractsRouter::class]
        ];
        foreach ($aliases as $key => $alias) {
            foreach ($alias as $concrete) {
               $this->aliases[$concrete] = $key;
            }
        }
    }

    public function getInstance()
    {
        return static::$instance;
    }

    public function register($provider)
    {
        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        $provider->register();

        // marked registered provider
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[get_class($provider)] = $provider;
    }

    public function bootstrapWith($bootstraps)
    {
        foreach ($bootstraps as $key => $bootstrap) {
           (new $bootstrap)->bootstrap($this);
        }
    }
}
