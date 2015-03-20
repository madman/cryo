<?php

namespace Core\Provider;

use Silex;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ModulesProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['modules'] = $app->share(function () use ($app) {
            $modules = new \Core\Modules($app);

            return $modules;
        });

        $app['modules']->exclude($app->config->get('modules/exclude'));

        foreach ($app->config->get('modules/paths') as $path) {
            // TODO: Refactor.
            // Catch only "Dir does not exists" exception.
            $app->modules->locate(CORE_ROOT_DIR . '/' . $path);
        }
    }

    public function boot(Application $app)
    {
    }

}
