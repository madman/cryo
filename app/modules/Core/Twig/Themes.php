<?php

namespace Core\Twig;

/**
 * Allows to use twig theming.
 * Example:
 * <pre>
 * $app->before(function () use ($app) {
 *   if ($app['device.detector']->isMobile()) {
 *     $app['twig.themes']->enable('sample_theme');
 *   }
 * });
 * </pre>
 */
class Themes
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function enable($name)
    {
        $path = CORE_ROOT_DIR . '/app/views/themes/' . $name;
        $this->app['twig.loader.filesystem']->setPaths([]);
        $this->app['twig.loader.filesystem']->prependPath($path);
    }

    public function register()
    {
        $this->app->before(function () {
            if ($this->app->config->get('twig/theme')) {
                $this->enable($this->app->config->get('twig/theme'));
            }
        });
    }
}
