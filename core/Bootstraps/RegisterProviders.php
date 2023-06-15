<?php
namespace Phucrr\Php\Bootstraps;

use App\Providers\RouteServiceProvider;
use Phucrr\Php\Application;

class RegisterProviders {
    public function bootstrap(Application $app)
    {
        $app->register(RouteServiceProvider::class);
    }
}