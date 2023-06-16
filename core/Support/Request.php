<?php
namespace Phucrr\Php\Support;

use Phucrr\Php\Contracts\RequestContract;

class Request implements RequestContract{

    /**
     * The params in query string
     * @var array
     */
    public $params = [];

    /**
     * The method HTTP request
     * 
     * @var string
     */
    protected $method;

    /**
     * The URI HTTP request
     * @var string
     */
    public $uri;

    /**
     * The full path URI request
     * @var string
     */
    public $path;

    /**
     * The host HTTP request
     */
    public $host;

    public function __construct()
    {
       $this->serialize();
    }

    private function serialize()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = array_merge($_GET, $_POST);
        $this->uri = $this->parseUri();
        $this->host = $_SERVER['HTTP_HOST'];
        $this->path = $this->parsePath();
    }

     /**
     * Parse uri of request
     * @return string
     */
    private function parseUri()
    {
        $uri = $_SERVER['REQUEST_URI'];

        $uri = trim(explode('?', $uri)[0], '/'); // split query string

        return $uri;
    }

    /**
     * Parse path of request
     * @return string
     */
    private function parsePath()
    {
        $uri = $_SERVER['REQUEST_URI'];

        $uri = trim(explode('?', $uri)[0], '/'); // split query string

        return $this->host .'/'. $uri;
    }

    /**
     * Return all params of request
     * @return array
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * Get param with specify key
     * @param string $key
     * @param string|null $default
     * 
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return $default;
    }

    /**
     * The method of request
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * The uri of request
     * @return string
     */
    public function uri()
    {
        if (!$this->uri) {
            return $this->parseUri();
        }
        return $this->uri;
    }
}