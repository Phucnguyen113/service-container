<?php
namespace Phucrr\Php;

use Phucrr\Php\Container;
use Phucrr\Php\Providers\RoutingServiceProvider;
use Phucrr\Php\Providers\ViewServiceProvider;


class Application extends Container{
    use Path;

    /**
     * All of the registered service providers
     */
    public $serviceProviders = [];

    /**
     * List names of loaded providers
     * @var array
     */
    public $loadedProviders = [];

    /**
     * Base path of application
     * @var string
     */
    public $basePath;
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        if ($path) {
            $this->setBasePath($path);
        }
        $this->bindingAliases();
        $this->bindingBasicProvider();
        $this->bindingBasic();
    }

    /**
     * Set the base path for application
     * 
     * @return null
     */
    public function setBasePath($path)
    {
        $this->basePath = rtrim($path, '\/');
        $this->bindPathsToContainer();
    }

    /**
     * Binding core path instances to container
     * 
     * @return null 
     */
    private function bindPathsToContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.resource', $this->resourcePath());
    }

    /**
     * Register core provider for the application
     * 
     * @return null
     */
    private function bindingBasicProvider()
    {
        $this->register(RoutingServiceProvider::class);
        $this->register(ViewServiceProvider::class);
    }

    /**
     * Singleton the Application instance to container
     * 
     * @return null
     */
    private function bindingBasic()
    {
        $this->singleton(Application::class, function () {
            return $this;
        });
        static::$instance = $this;
    }

    /**
     * Register the core aliases
     * 
     * @return null
     */
    private function bindingAliases()
    {
        $aliases = [
            'route' => [\Phucrr\Php\Route\Router::class, \Phucrr\Php\Contracts\Router::class],
            'view' => [\Phucrr\Php\Support\View::class]
        ];
        foreach ($aliases as $key => $alias) {
            foreach ($alias as $concrete) {
               $this->aliases[$concrete] = $key;
            }
        }
    }

    /**
     * Get the Application instance
     * 
     * @return self
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Resolve and register the provider
     *
     * @param string|object $provider
     *
     * @return null
     */
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
