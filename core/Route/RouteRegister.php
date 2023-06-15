<?php
namespace Phucrr\Php\Route;

class RouteRegister {
    public $router;

    public $attributes = [];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function attribute($key, $value){
        $this->attributes[$key] = $value;
        return $this;
    }

    public function group($callback)
    {
        return $this->router->group($this->attributes, $callback);
    }
}