<?php
namespace Phucrr\Php\Support;

use Phucrr\Php\Contracts\RequestContract;

class Request implements RequestContract{

    public $params = [];

    protected $method;
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = array_merge($_GET, $_POST);
    }
    public function all()
    {
        return $this->params;
    }

    public function get($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return $default;
    }
}