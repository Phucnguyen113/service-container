<?php
namespace Phucrr\Php\Route;

use Closure;
use Phucrr\Php\Application;
use Phucrr\Php\Contracts\RequestContract;
use Phucrr\Php\Contracts\Router as ContractsRouter;

class Router implements ContractsRouter {

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    public $groupStack = [];

    /**
     * The route collection instance.
     *
     * @var RouteCollection
     */
    public $routes;

    /**
     * The request currently being dispatched.
     *
     * @var \Phucrr\Php\Support\Request
     */
    public $request;

    /**
     * The Application instance
     * 
     * @var Application
     */
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

    /**
     * Create a new route instance.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  mixed  $action
     *
     * @return Route
     */
    protected function createRoute($method, $uri, $action)
    {   
        $uri = $this->prefix($uri);
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }
        $route = (new Route([$method], $uri, $action))->setContainer($this->app);
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

    /**
     * Prepend the prefix to uri
     * 
     * @param string $uri
     * 
     * @return string
     */
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

    /**
     * Create a route group with shared attributes.
     *
     * @param  array  $attributes
     * @param  \Closure|string  $routes
     * @return void
     */
    public function group(array $attributes, $callback)
    {
        $this->updateGroupStack($attributes);

        $this->loadRoutes($callback);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     * @return void
     */
    public function updateGroupStack($attributes)
    {
        if (! empty($this->groupStack)) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }
        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     * 
     * @param  array  $new
     * @return array
     */
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
        $oldNamespace = $old['namespace'] ?? null;

        if (isset($new['namespace'])) {
            return isset($old['namespace']) && strpos($new['namespace'], '\\') !== 0
            ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
            : trim($new['namespace'], '\\');
        }
        return $oldNamespace;
    }

    /**
     * Load register routes from file|Closure
     * 
     * @param string|\Closure @callback
     * 
     * @return null
     */
    public function loadRoutes($callback)
    {
        if ($callback instanceof \Closure) {
            $callback($this);
        } else {
            $router = $this;
            require $callback;
        }
    }

    /**
     * Set Request for Router
     * 
     * @param RequestContract $request
     * 
     * @return $this
     */
    public function setRequest(RequestContract $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Dispatch request to router
     * @return null
     */
    public function dispatch()
    {
        return $this->runRoute($this->findRoute());
    }

    /**
     * Run route, which matched with request
     * 
     * @param Route $route
     * 
     * @throws \Exception
     *
     * @return mixed
     */
    protected function runRoute($route)
    {
        if (!$route instanceof Route) {
            throw new \Exception("not found Route exception", 1);
        }
        return $route->run();
    }
    
    /**
     * Find a route matched with request uri
     * 
     * @return Route
     */
    protected function findRoute()
    {
        return $this->routes->match($this->request);
    }

    /**
     * Call dynamic method in RouteRegister
     */
    public function __call($method, $arguments)
    {
        return (new RouteRegister($this))->attribute($method, $arguments);
    }
}