<?php

namespace Storage;

use Core\Application;
use Core\Interfaces\ModuleInterface;
use Admin\Interfaces\AdminMenuManagerInterface;

class Module implements ModuleInterface {

    protected $app;

    public function initialize(Application $app) {
        $app->router->addRoutes(__DIR__ . '/Resources/config/frontend_routes.yml');
        $this->registerProviders($app);
    }

    protected function registerProviders($app) {
        $app['db.bloods'] = $app->share(function () use ($app) {
            return new Entity\BloodMapper($app['db']);
        });
                
                
        /*$app['db.orders'] = $app->share(
                function () use ($app) {
                    return new OrderMapper($app['db']);
                });*/
    }

    public function registerMenuItems(AdminMenuManagerInterface $manager) {
        
    }
}
