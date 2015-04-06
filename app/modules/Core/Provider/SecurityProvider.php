<?php

namespace Core\Provider;

use Silex,
    Silex\Application,
    Silex\ServiceProviderInterface,
    Core\DefaultAuthenticationSuccessHandler,
    Core\DefaultAuthenticationFailureHandler;

class SecurityProvider implements ServiceProviderInterface {

    public function register(Application $app) {
       //*
        $app->register(new Silex\Provider\SecurityServiceProvider(), [
            'security.firewalls' => [
                'profiler' => [
                    'pattern' => '^/_profiler',
                    'anonymous' => true,
                ],
                'login' => array(
                    'pattern' => '^/users/login$',
                    'anonymous' => true,
                ),
                'default' => [
                    'pattern' => '^/',
                    'form' => [
                        'login_path' => '/users/login',
                        'check_path' => '/users/secured'
                    ],
                    'logout' => [
                        'logout_path' => '/users/logout',
                    ],
                    'users' => $app->share(function () use ($app) {
                        return new \Users\Provider\UserProvider($app['db']);
                    }),
                ]
        ]]); // */

        //$app->register(new Silex\Provider\RememberMeServiceProvider);
    }

    public function boot(Application $app) {
        
    }

}
