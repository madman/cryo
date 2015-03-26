<?php

namespace Startpage;

use Core\Application;
use Core\Interfaces\ModuleInterface;
use Admin\Interfaces\AdminMenuManagerInterface;

class Module implements ModuleInterface
{

    public function initialize(Application $app)
    {
        $app->router->addRoutes(__DIR__ . '/Resources/config/routes.yml');
    }

    public function registerMenuItems(AdminMenuManagerInterface $manager)
    {
    }
}
