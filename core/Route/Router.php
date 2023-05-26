<?php
namespace Phucrr\Php\Route;

use Phucrr\Php\Contracts\Router as ContractsRouter;

class Router implements ContractsRouter {
    
    protected static $prefix = '';

    public $routes = [];


    public function __construct()
    {
        // $this->loadRoute();
    }
    /**
     * @inheritdoc
     */
    public function addRoute($method, $uri, $action)
    {
        $this->routes[] = compact('method', 'uri', 'action');
    }

     /**
     * @inheritdoc
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

     /**
     * @inheritdoc
     */
    public function post($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function group($prefix, $callback)
    {
        $originalPrefix = self::$prefix;

        self::$prefix .= ltrim($prefix, '/') . '/';
        $callback();
        self::$prefix = $originalPrefix;
    }

    public function loadRoute()
    {
        if (is_file(__DIR__.'/../../route/web.php')) {
            require __DIR__.'/../../route/web.php';
        }
    }
}