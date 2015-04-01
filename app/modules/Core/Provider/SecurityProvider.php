<?php

namespace Core\Provider;

use Silex,
    Silex\Application,
    Silex\ServiceProviderInterface,
    Core\DefaultAuthenticationSuccessHandler,
    Core\DefaultAuthenticationFailureHandler;

class SecurityProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        //TODO: after db provider
        /*
        $app->register(new Silex\Provider\SecurityServiceProvider(), [
            'security.firewalls' => [
                'default' => [
                    'pattern' => '^/',
                    'pattern' => '^/',
                    'form' => [
                        'login_path' => '/users/login',
                        'check_path' => '/users/secured'
                    ],
                    'logout' => [
                        'logout_path' => '/users/logout',
                    ],
                    'users' => $app->share(function () {
                        return new \Users\Provider\UserProvider;
                    }),
                ]
        ]]);

        $app->register(new Silex\Provider\RememberMeServiceProvider);
         * 
         */
    }

    public function boot(Application $app) {
        
    }

}
