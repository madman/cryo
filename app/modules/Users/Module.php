<?php

namespace Users;

use Core\Application;
use Core\Interfaces\ModuleInterface;
use Admin\Interfaces\AdminMenuManagerInterface;
use Users\Entity\UserMapper;

class Module implements ModuleInterface {

    protected $app;

    public function initialize(Application $app) {
        $this->app = $app;

        $this->app->router->addRoutes(__DIR__ . '/Resources/config/frontend_routes.yml');
        $this->registerProviders();
    }

    protected function registerProviders() {
        
        $app = $this->app;

        $app['db.users'] = $app->share(
                function () use ($app) {
                    return new UserMapper($app['db']);
                });

        //$this->app->register(new Provider\RegisterFormProvider);
    }

    public function registerMenuItems(AdminMenuManagerInterface $manager) {
        
    }

    public function registerConsole($console) {
        new \Users\Console\UsersCommand($console);
    }

}
