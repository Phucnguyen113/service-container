<?php
namespace Phucrr\Php\Route;

class RouteRegister {

    /**
     * The router instance.
     *
     * @var \Phucrr\Php\Route\Router
     */
    public $router;

    /**
     * The attributes to pass on to the router.
     *
     * @var array
     */
    public $attributes = [];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Set the value for a given attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function attribute($key, $value){
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public function group($callback)
    {
        return $this->router->group($this->attributes, $callback);
    }

    public function __call($method, $parameters)
    {
        return $this->attribute($method, $parameters[0]);
    }
}