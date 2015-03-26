<?php

namespace Core\Provider;

use Silex,
    Silex\Application,
    Silex\ServiceProviderInterface,
    Core\DefaultAuthenticationSuccessHandler,
    Core\DefaultAuthenticationFailureHandler;

class SecurityProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new Silex\Provider\SecurityServiceProvider(), [
            'security.firewalls' => [
                'default' => [
                    'pattern'     => '^/',
                    'anonymous'   => true,
                    'form'        => [
                        'login_path' => '/users/login',
                        'check_path' => '/users/secured'
                    ],
                    'logout'      => [
                        'logout_path' => '/users/logout',
                    ],
                    'remember_me' => [
                        'key'                => __FILE__,
                        'always_remember_me' => true
                    ],
                    'users' => $app->share(function () {
                        return new \Users\Provider\UserProvider;
                    }),
                ]
            ]]);

        $app->register(new Silex\Provider\RememberMeServiceProvider);
    }

    public function boot(Application $app)
    {
    }
}
