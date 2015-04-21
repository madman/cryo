<?php

namespace Core;

use Core\Application,
    Core\Interfaces\ModuleInterface,
    Core\Console;
use Admin\Interfaces\AdminMenuManagerInterface;

class Module implements ModuleInterface
{

    protected $app;

    public function initialize(Application $app)
    {

        $this->app = $app;

        $app['top.menu.items'] = $app->share(function () use ($app) {
            return [
                'Всі користувачі' => $this->app->path('users/list'),
                'Створити нового користувача' => $this->app->path('users/create'),
                'Матеріали' => $this->app->path('blood/list'),
                'Додати новий матеріал' => $this->app->path('blood/add'),
            ];
        });

        $app->before(function () use ($app) {
            $app->twig->addExtension(new Twig\TopMenuExtension($app));
        });
    }

    public function registerMenuItems(AdminMenuManagerInterface $manager)
    {
    }

    public function registerConsole($console)
    {
        new Console\RoutesCommand($console);
        new Console\MigrateCommand($console);
        new Console\PayoutCommand($console);
    }
}


