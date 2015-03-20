<?php

namespace Users\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Users\Component\RegisterFormFactory;

class RegisterFormProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['user.register.form.factory'] = $app->share(
            function () use ($app) {
                return new RegisterFormFactory(
                    $app['form.factory'],
                    $app['dispatcher'],
                    $app->currencies,
                    $app['qs.cookie.value']()
                );
            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
} 