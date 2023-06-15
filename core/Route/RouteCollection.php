<?php
namespace Phucrr\Php\Route;

use Phucrr\Php\Contracts\RequestContract;

class RouteCollection {

    public $routes = [];
    public function __construct()
    {
        
    }

    public function add(Route $route)
    {
        $this->addCollection($route);
        return $route;
    }

    public function addCollection(Route $route)
    {   $fullUri = $route->uri();
        foreach ($route->methods() as $key => $method) {
           $this->routes[$method][$fullUri]= $route;
        }
    }

    public function match(RequestContract $request)
    {
        $routes = $this->routes[$request->method()] ?? [];

        $route = $this->matchRouteAgain($routes, $request);
        return $route;
    }

    protected function matchRouteAgain(array $routes, RequestContract $request)
    {
        foreach ($routes as $key => $route) {
            if ($route->match($request)) {
                return $route;
            }
        }

    }
}