<?php
namespace Phucrr\Php\Route;

use Phucrr\Php\Container;
use Phucrr\Php\Contracts\RequestContract;
use ReflectionFunction;
use ReflectionMethod;

class Route {

    /**
     * The HTTP methods the route responds to.
     *
     * @var array
     */
    public $methods = [];

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The route action array.
     *
     * @var array
     */
    public $action;

    /**
     * Params of route be mapped with request
     * 
     * @var array
     */
    public $params = [];

    /**
     * The container instance
     * 
     * @var Container
     */
    public $container;

    public function __construct(array $methods, string $uri, $action)
    {
        $this->methods = $methods;
        $this->uri = trim($uri, '/');
        $this->action = $this->parseAction($action);

        // $this->prefix($action);
    }

    /**
     * Parse action to route
     * 
     * @param closure|array $action
     * 
     * @return array
     */
    private function parseAction($action)
    {
        if (is_callable($action)) {
            return ['uses' => $action];
        }
        return $action;
    }

    /**
     * Add prefix to route
     *
     * @return $this
     */
    public function prefix($prefix)
    {
        if ($prefix) {
            $this->uri = trim($prefix, '/') . '/' . trim($this->uri, '/'); 
        }
        return $this;
    }
    
    public function setAction(array $action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the HTTP verbs the route responds to.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Get the URI associated with the route.
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Run route, dispatch to controller or run closure action
     * @return null
     */
    public function run()
    {
        if (is_string($this->action['uses'])) {
            return $this->dispatchToController();
        }

        return $this->runCallable();
    }

    /**
     * Dispatch request to specify controller
     * @return null
     */
    private function dispatchToController()
    {
        [$controller, $method] = $this->parseControllerAndMethod();

        $controller = $this->container->make(ltrim($controller, '\\'));
        
        $controller->$method(...$this->resolveControllerMethodDependencies($controller, $method));
    }

    /**
     * Get controller and method of route
     * @return array
     */
    private function parseControllerAndMethod()
    {
        list($controller, $method) = explode('@', $this->action['uses']);
        return [$controller, $method];
    }

    /**
     * Resolve dependencies method controller
     * 
     * @param object $controller
     * @param string $method
     * 
     * @return array
     */
    private function resolveControllerMethodDependencies($controller, $method)
    {
        $parameters = $this->getParams();
        $values = array_values($parameters);
        $reflection = new ReflectionMethod($controller, $method);
        $instanceCount = 0;
        foreach ($reflection->getParameters() as $key => $param) {
            if ($param->getClass()) {
                $instance = $this->container->make($param->getClass()->name);
                $this->replaceParam($parameters, $key, $instance);
                $instanceCount++;
            } else if (!isset($values[$key - $instanceCount]) && $param->isDefaultValueAvailable()) {
                $this->replaceParam($parameters, $key, $param->getDefaultValue());
            }
        }

        return $parameters;
    }

    /**
     * Replace params in route be mapped with params request
     * To bind in controller|closure
     * 
     * @param array &$params
     * @param int $offset
     * @param string $replacement
     */
    private function replaceParam(&$params, $offset, $replacement)
    {
        array_splice($params, $offset, 0, [$replacement]);
    }

    /**
     * Get params of route be mapped with request
     * @return array
     */
    private function getParams()
    {
        //binding order by index
        $parameters = array_filter($this->params, function ($key) {
            return is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);

        return $parameters;
    }

    /**
     * Run closure action
     * @return null
     */
    private function runCallable()
    {
        $callable = $this->action['uses'];
        return $callable(...$this->resolveFunctionDependencies($callable));
    }

    /**
     * Resolve closure action dependencies
     * 
     * @param \Closure $function
     * 
     * @return array
     */
    private function resolveFunctionDependencies($function)
    {
        $parameters = $this->getParams();
        $values = array_values($parameters);
        $reflection = new ReflectionFunction($function);
        $instanceCount = 0;
        foreach ($reflection->getParameters() as $key => $param) {
            if ($param->getClass()) {
                $instance = $this->container->make($param->getClass()->name);
                $this->replaceParam($parameters, $key, $instance);
                $instanceCount++;
            } else if (!isset($values[$key - $instanceCount]) && $param->isDefaultValueAvailable()) {
                $this->replaceParam($parameters, $key, $param->getDefaultValue());
            }
        }

        return $parameters;
    }

    /**
     * Get param names of route
     * @return array
     */
    private function getParamNames()
    {
        $uri = $this->uri();

        $pattern = '/\{(\w+)\}/';

        preg_match_all($pattern, $uri, $matches);
        return isset($matches[1]) ? $matches[1] : [];
    }

    /**
     * Check route have params
     * @return bool
     */
    private function haveParams()
    {
        return !empty($this->params);
    }

    /**
     * Match route registered with request
     * 
     * @param RequestContract $request
     * 
     * @return bool
     */
    public function match(RequestContract $request)
    {
        $requestUri = $request->uri();
        $routeUri = $this->uri();
        $pattern = $this->buildPattern($routeUri);
        $matched = preg_match($pattern, $requestUri, $matches);
        if ($matched) {
            array_shift($matches);
            $this->addBindingParams($matches);
            return true;
        }
        return false;
    }

    /**
     * Build pattern from uri route to match with request uri
     * 
     * @param string $uri
     * 
     * @return string
     */
    protected function buildPattern($uri)
    {
        $pattern = str_replace('/', '\/', $uri);
        $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>[^\/]+)', $pattern);

        return '/^'.$pattern.'$/';
    }

    /**
     * Add params from request to route when matched uri route 
     * 
     * @param array params
     * 
     * @return null
     */
    protected function addBindingParams($params)
    {
        $this->params = $params;
    }

    /**
     * Set container for route
     * 
     * @param Container $container
     * 
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }
}
