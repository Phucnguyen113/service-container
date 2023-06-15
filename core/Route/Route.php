<?php
namespace Phucrr\Php\Route;

use Phucrr\Php\Contracts\RequestContract;

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

    public $params = [];

    public function __construct(array $methods, string $uri, $action)
    {
        $this->methods = $methods;
        $this->uri = trim($uri, '/');
        $this->action = $this->parseAction($action);

        // $this->prefix($action);
    }

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
     * @return null
     */
    public function prefix($action)
    {
        $prefix = $action['prefix'] ?? '';

        if ($prefix) {
            $this->uri = trim($prefix, '/') . '/' . trim($this->uri, '/'); 
        }
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

    public function run()
    {

    }

    public function match(RequestContract $request)
    {
        $requestUri = $request->uri();
        $routeUri = $this->uri();
        $pattern = $this->buildPattern($routeUri);
        $matched = preg_match_all($pattern, $requestUri, $matches);
        
        if ($matched) {
            array_shift($matches);
            $this->addBindingParams($matches);
            return true;
        }
        return false;
    }

    protected function buildPattern($uri)
    {
        
        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', $uri);
        $pattern = str_replace('/', '\/', $pattern);

        return '/^'.$pattern.'$/';
    }

    protected function addBindingParams($params)
    {
        $this->params = $params;
    }
}
