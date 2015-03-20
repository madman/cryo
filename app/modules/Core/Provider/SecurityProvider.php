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
                'login_admin'   => [
                    'pattern'     => '^/login-admin',
                    'anonymous'   => true,
                ],
                'admin'   => [
                    'pattern'     => '^/admin',
                    'anonymous'   => false,
                    'form'        => [
                        'login_path' => '/login-admin',
                        'check_path' => '/admin/secured'
                    ],
                    'logout'      => [
                        'logout_path' => '/admin/logout',
                    ],
                    'remember_me' => [
                        'key'                => __FILE__ . 'admin',
                        'always_remember_me' => true
                    ],
                    'users' => $app->share(function () {
                        return new \Users\Provider\AdminUserProvider;
                    }),
                ],
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

        $app['security.authentication.success_handler._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($name, $options, $app) {
                $handler = new DefaultAuthenticationSuccessHandler(
                    $app['security.http_utils'],
                    $options
                );
                $handler->setProviderKey($name);

                return $handler;
            });
        });

        $app['security.authentication.failure_handler._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($name, $options, $app) {
                return new DefaultAuthenticationFailureHandler(
                    $app,
                    $app['security.http_utils'],
                    $options,
                    $app['logger']
                );
            });
        });

        $app->register(new Silex\Provider\RememberMeServiceProvider);
    }

    public function boot(Application $app)
    {
    }
}
