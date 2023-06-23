<?php
namespace Phucrr\Php\Route;

use Phucrr\Php\Contracts\RequestContract;

class RouteCollection {

    /**
     * The registered routes 
     * @var array
     */
    public $routes = [];
    public function __construct()
    {
        
    }

    /**
     * Add the route to the route collection
     * 
     * @param Route $route
     * 
     * @return Route
     */
    public function add(Route $route)
    {
        $this->addCollection($route);
        return $route;
    }

    /**
     * Add the route to the route collect
     * 
     * @param Route $route
     * 
     * @return null
     */
    public function addCollection(Route $route)
    {   $fullUri = $route->uri();
        foreach ($route->methods() as $key => $method) {
           $this->routes[$method][$fullUri]= $route;
        }
    }

    /**
     * Find the route matched with request
     * 
     * @param RequestContract $request
     * 
     * @return Route|null
     */
    public function match(RequestContract $request)
    {
        $routes = $this->routes[$request->method()] ?? [];

        $route = $this->matchRouteAgain($routes, $request);
        return $route;
    }

    /**
     * Find the route again in array routes matched with method request
     * 
     * @param array $routes
     * @param RequestContract $request
     * 
     * @return Route|null
     */
    protected function matchRouteAgain(array $routes, RequestContract $request)
    {
        foreach ($routes as $key => $route) {
            if ($route->match($request)) {
                return $route;
            }
        }

    }
}