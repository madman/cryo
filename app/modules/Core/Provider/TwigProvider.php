<?php

namespace Core\Provider;

use Silex;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Core\Twig\Filter\RemoveDangerousContentFilter;
use Core\Twig\Themes;

class TwigProvider implements ServiceProviderInterface
{
    protected $app;

    public function register(Application $app)
    {
        $this->app = $app;

        $app->register(new Silex\Provider\TwigServiceProvider, [
            'twig.options' => [
                'debug'            => $app['debug'],
                'cache'            => CORE_RUNTIME_DIR . '/views',
                'strict_variables' => true,
                'autoescape'       => true,
            ],
        ]);

        foreach ($app->config->get('twig/paths') as $path) {
            $this->appendViewsDirectory(CORE_ROOT_DIR . '/' . $path);
        }

        $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig, Application $app) {
            $twig->addFilter(new RemoveDangerousContentFilter());

            return $twig;
        }));

        $app['twig.themes'] = $app->share(function () {
            $themes = new Themes($this->app);

            return $themes;
        });
        $app['twig.themes']->register();
    }

    public function appendViewsDirectory($path)
    {
        if (file_exists($path)) {
            $this->app['twig.loader.filesystem']->addPath($path);
        } else {
            $this->app->monolog->addInfo("Views directory $path exist. Ignoring");
        }
    }

    public function boot(Application $app)
    {
    }
}
