<?php

namespace Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MongoProvider implements ServiceProviderInterface
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function register(Application $app)
    {
        foreach ($this->config as $dbName => $data) {
            $app['mongo.client.' . $dbName] = $app->share(function () use ($data) {
                return new \MongoClient($data['server']);
            });

            $app['mongo.' . $dbName] = $app->share(function () use ($app, $data, $dbName) {
                $db = $data['db'];
                return $app['mongo.client.' . $dbName]->$db;
            });
        }
    }

    public function boot(Application $app)
    {
    }

}
