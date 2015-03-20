<?php

namespace Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Core\EnvironmentDetector;

class EnvironmentDetectorProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['env'] = $app->share(function () use ($app) {
            return new EnvironmentDetector;
        });
    }

    public function boot(Application $app)
    {
    }
}
