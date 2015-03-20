<?php

namespace Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigProvider implements ServiceProviderInterface
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function register(Application $app)
    {
        $app['config'] = $app->share(function () {
            return new \Core\Config($this->config);
        });
    }

    public function boot(Application $app)
    {
    }

}
