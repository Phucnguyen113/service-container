<?php

use App\Http\Kernel\Kernel as HttpKernel;
use Phucrr\Php\kernel\Kernel;
use Phucrr\Php\Contracts\RequestContract;
use Phucrr\Php\Support\Request;

    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $app->bind(Kernel::class, HttpKernel::class);
    $app->bind(RequestContract::class, Request::class);
    $kernel = $app->make(Kernel::class);
    $response = $kernel->handle($app->make(RequestContract::class));
    echo $response;
    // $response->send();