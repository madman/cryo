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
