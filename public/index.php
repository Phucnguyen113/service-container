<?php

use App\Http\Kernel\Kernel as HttpKernel;
use Phucrr\Php\kernel\Kernel;
use Phucrr\Php\Contracts\RequestContract;
use Phucrr\Php\Support\Facades\Route;
use Phucrr\Php\Support\Request;

    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->bind(Kernel::class, HttpKernel::class);
    $app->bind(RequestContract::class, Request::class);
    $kernel = $app->make(Kernel::class);
    $kernel->handle($app->make(RequestContract::class));
 
    // echo '<pre>';
    // var_dump($app->instances['route']->routes->routes);

    // interface a {

    // }
    // class child1 implements a {
    
    // }
    
    // class child2 implements a {
    
    // }
    // echo '<pre>';
    // $app->when('AController')->needs(a::class)->give(child1::class);
    // $app->when('BController')->needs(a::class)->give(child2::class);
    // // $router = $app->make('router');
    // // var_dump($app, $router);
    // class AController {
    //     public $a;
    //     public function __construct(a $a)
    //     {
    //         $this->a = $a;
    //     }
    // }
    
    // class BController {
    //     public $a;
    //     public function __construct(a $a)
    //     {
    //         $this->a = $a;
    //     }
    // }
    // print_r($app->make(BController::class));
    // print_r($app->make(AController::class));