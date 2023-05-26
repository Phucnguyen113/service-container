<?php
namespace Phucrr\Php\Support\Facades;

use Phucrr\Php\Application;

abstract class Facade {
    
    public static $app;

    public static function getFacadeAccessor()
    {
        throw new \Exception('You have to binding the facade name');
    }

    public static function __callStatic($method, $params)
    {
        return self::$app->make(static::getFacadeAccessor())->$method(...$params);
    }

    public static function setFacadeApplication(Application $app) {
        self::$app = $app;
    }
}