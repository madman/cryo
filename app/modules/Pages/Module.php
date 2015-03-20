<?php

namespace Pages;

use Admin\Interfaces\AdminMenuManagerInterface;
use Core\Application;
use Core\Interfaces\ModuleInterface;
use Pages\Twig\HighlightFilter;
use Pages\Twig\SortableTableHeadFunction;

class Module implements ModuleInterface
{
    protected $app;

    public function initialize(Application $app)
    {
        $this->app = $app;
        $app->router->addRoutes(__DIR__ . '/Resources/config/backend_routes.yml');
        $app->router->addRoutes(__DIR__ . '/Resources/config/frontend_routes.yml');

        $this->registerTwig();
    }

    protected function registerTwig()
    {
        // we can move it to admin and
        $this->app->before(function () {
            // we need the following string for include paginator. @todo: remove this string after we change pagination way
            $this->app['twig.loader.filesystem']->addPath(__DIR__ . '/Resources', 'Pages');

            $this->app->twig->addFilter(new HighlightFilter(['is_safe' => ['html']]));
            $this->app->twig->addFunction(new SortableTableHeadFunction());
        });
    }

    public function registerMenuItems(AdminMenuManagerInterface $manager)
    {
        $manager->addTo($this->app->trans('Контент'), [
            $this->app->trans('Статические страницы') => $this->app->path('admin/pages/index'),
            $this->app->trans('Акции') => $this->app->path('admin/actions/index'),
            $this->app->trans('Новости') => $this->app->path('admin/news/index'),
        ]);
    }

    public function registerConsole($console)
    {
        new \Pages\Console\PagesCommand($console);
    }
}