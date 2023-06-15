<?php
namespace Phucrr\Php\Route;

use Closure;
use Phucrr\Php\Application;
use Phucrr\Php\Contracts\RequestContract;
use Phucrr\Php\Contracts\Router as ContractsRouter;

class Router implements ContractsRouter {
    
    protected static $prefix = '';

    public $groupStack = [];

    public $routes;

    public $request;
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->routes = new RouteCollection;
    }
    /**
     * @inheritdoc
     */
    public function addRoute($method, $uri, $action)
    {
        return $this->routes->add($this->createRoute($method, $uri, $action));
    }

    protected function createRoute($method, $uri, $action)
    {   
        $uri = $this->prefix($uri);
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }
        $route = new Route([$method], $uri, $action);
        if (!empty($this->groupStack)) {
            $this->mergeLastGroupToRoute($route);
        }
        return $route;
    }

    /**
     * Merge last group stack to route
     * 
     * @param Route $route
     */
    protected function mergeLastGroupToRoute(Route $route)
    {
        $route->setAction($this->mergeWithLastGroup($route->getAction()));
    }

    /**
     * Determine if the action is routing to a controller
     * @param array|closure|string $action
     * 
     * @return bool
     */
    protected function actionReferencesController($action)
    {
        if (!$action instanceof Closure) {
            return is_string($action) || (isset($action['uses']) && is_string($action['uses']));
        }
        return false;
    }

    /**
     * Add a controller based route action to the action array.
     *
     * @param  array|string  $action
     * @return array
     */
    protected function convertToControllerAction($action)
    {
        if (is_string($action)) {
            $action = ['uses' => $action];
        }

        if (!empty($this->groupStack)) {
            $action['uses'] = $this->prependGroupNamespace($action['uses']);
        }
        $action['controller'] = $action['uses'];
        return $action;
    }

    /**
     * Prepend the last group namespace onto the use clause.
     *
     * @param  string  $class
     * @return string
     */
    protected function prependGroupNamespace($class)
    {
        $group = end($this->groupStack);

        return isset($group['namespace']) && strpos($class, '\\') !== 0
                ? $group['namespace'].'\\'.$class : $class;
    }

    protected function prefix($uri)
    {
        return trim($this->getLastGroupPrefix(), '/'). '/'. trim($uri, '/') ?: '/';
    }

    protected function getLastGroupPrefix()
    {
        $group = end($this->groupStack);
        return $group['prefix'] ?? '';
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
        $this->addRoute('POST', $uri, $action);
    }

    public function group(array $attributes, $callback)
    {
        $this->updateGroupStack($attributes);

        $this->loadRoutes($callback);

        array_pop($this->groupStack);
    }

    public function updateGroupStack($attributes)
    {
        if (! empty($this->groupStack)) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }
        $this->groupStack[] = $attributes;
    }

    protected function mergeWithLastGroup($new)
    {
        $lastGroup = end($this->groupStack);
        
        $new['prefix'] = $this->mergePrefix($new, $lastGroup);
        $new['namespace'] = $this->mergeNamespace($new, $lastGroup);
        $except = ['namespace', 'prefix'];

        return array_merge_recursive(array_filter($lastGroup, function ($key) use ($except) {
            return !in_array($key, $except);
        }, ARRAY_FILTER_USE_KEY), $new);
         
    }

    /**
     * Merge new prefix with last group prefix
     * @param array $new
     * @param array @old
     * 
     * @return string|null
     */
    protected function mergePrefix($new, $old)
    {
        $oldPrefix = $old['prefix'] ?? null;

        $prefix = isset($new['prefix']) ? trim($oldPrefix, '/').'/'.trim($new['prefix'], '/') : $oldPrefix;
        return $prefix;
    }

    /**
     * Merge new namespace with last group prefix
     * @param array $new
     * @param array @old
     * 
     * @return string|null
     */
    protected function mergeNamespace($new, $old)
    {
        $oldPrefix = $old['namespace'] ?? null;

        if (isset($new['namespace'])) {
            return isset($old['namespace']) && strpos($new['namespace'], '\\') !== 0
            ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
            : trim($new['namespace'], '\\');
        }
        return $oldPrefix;
    }

    public function loadRoutes($callback)
    {
        if ($callback instanceof \Closure) {
            $callback($this);
        } else {
            $router = $this;
            require $callback;
        }
    }

    public function setRequest(RequestContract $request)
    {
        $this->request = $request;
        return $this;
    }

    public function dispatch()
    {
        $this->runRoute($this->findRoute());
    }

    protected function runRoute($route)
    {
        if (!$route instanceof Route) {
            throw new \Exception("not found exception", 1);
        }
        $route->run();
    }

    protected function findRoute()
    {
        return $this->routes->match($this->request);
    }

    public function __call($method, $arguments)
    {
        return (new RouteRegister($this))->attribute($method, $arguments);
    }
}