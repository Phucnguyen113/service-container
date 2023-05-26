<?php
namespace Phucrr\Php\Contracts;

interface Router {

    /**
     * Add route in router instance
     *
     * @param string $method GET|POST
     * @param string $uri
     * @param string|Closure $action
     * 
     * @return null
     */
    public function addRoute($method, $uri, $action);

    /**
     * Add route in router instance with method GET
     *
     * @param string $uri
     * @param string|Closure $action
     * 
     * @return null
     */
    public function get($uri, $action);

    /**
     * Add route in router instance with method POST
     *
     * @param string $uri
     * @param string|Closure $action
     * 
     * @return null
     */
    public function post($uri, $action);

    /**
     * Load routes
     */
    public function loadRoute();
}