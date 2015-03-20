<?php

namespace Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RouterProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['router'] = $app->share(function () use ($app) {
            return new \Core\Router($app);
        });
    }

    public function boot(Application $app)
    {
    }

}
