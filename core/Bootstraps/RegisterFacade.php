<?php
namespace Phucrr\Php\Bootstraps;

use Phucrr\Php\Application;
use Phucrr\Php\Support\Facades\Facade;

class RegisterFacade {

    public function bootstrap(Application $app)
    {
        Facade::setFacadeApplication($app);
    }
}