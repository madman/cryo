<?php

namespace Core;

use Symfony\Component\Console\Application as ConsoleApplication;

class Console extends ConsoleApplication
{
    public $app;

    public function __construct($app, $name = 'neocore', $version = '1.0')
    {
        $this->app = $app;
        parent::__construct($name, $version);

        $this->registerCommands();
    }

    public function registerCommands()
    {
        foreach ($this->getModules() as $module) {
            if (method_exists($module, 'registerConsole')) {
                $module->registerConsole($this);
            }
        }
    }

    public function getModules()
    {
        return $this->app->modules->getModules();
    }
}
