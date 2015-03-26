<?php

namespace Users;

use Core\Application;
use Core\Interfaces\ModuleInterface;
use Admin\Interfaces\AdminMenuManagerInterface;

class Module implements ModuleInterface
{
    protected $app;

    public function initialize(Application $app)
    {
        $this->app = $app;
        $app->router->addRoutes(__DIR__ . '/Resources/config/backend_routes.yml');
        $app->router->addRoutes(__DIR__ . '/Resources/config/frontend_routes.yml');
        //$this->registerProviders();
        //$this->registerEvents();
        //$this->registerUserSettings();
        //$this->registerRest();
    }

    protected function registerProviders()
    {
        $this->app->register(
            new Provider\UserBalanceProvider,
            [
                'user.balance.real.collection'     => 'users_balance_real',
                'user.balance.fun.collection'      => 'users_balance_fun',
                'user.balance.real.log.collection' => 'users_balance_real_log'
            ]
        );

        $this->app->register(new Provider\StatsProvider);
        $this->app->register(new Provider\RegisterFormProvider);
    }

    public function registerMenuItems(AdminMenuManagerInterface $manager)
    {
        $manager->addTo(
            $this->app->trans('Пользователи'),
            [
                $this->app->trans('Все пользователи') => $this->app->path('admin/users/index'),
            ]
        );
    }

    public function registerUserSettings()
    {
        $profileItems = [];
        $items        = [];
        if (isset($this->app['user.profile.items'])) {
            $items = $this->app['user.profile.items'];
        }

        $this->app['user.profile.items'] = array_merge($profileItems, $items);
    }

    protected function registerEvents()
    {
        new EventHandlers($this->app);
    }

    public function registerRest()
    {
        $players = $this->app['rest']->resource('/api/players', new Controller\Rest\IndexController($this->app))
            ->action('/bonuses', 'get', 'actionBonuses')
            ->action('/coupons', 'get', 'actionCoupons');

        $players->resource('/balance/real/history', new Controller\Rest\BalanceHistoryController($this->app), 'sub_id');
        $players->resource('/balance/real', new Controller\Rest\BalanceController($this->app), 'sub_id');
        $players->resource('/balance/bonuses/history', new Controller\Rest\BonusesHistoryController($this->app), 'sub_id');
        $players->resource('/balance/bonuses', new Controller\Rest\BonusesController($this->app), 'sub_id');
        $players->resource('/balance/compoints', new Controller\Rest\CompointsController($this->app), 'sub_id');
        $players->resource('/phones', new Controller\Rest\PhonesController($this->app), 'phoneId');
        $players->resource('/comments', new Controller\Rest\CommentsController($this->app), 'commentId');
        $players->resource('/bonusoffers', new Controller\Rest\BonusOffersController($this->app), 'offerId');
        $players->resource('/portraits', new Controller\Rest\PortraitsController($this->app), 'portraitId');
        $players->resource('/identified', new Controller\Rest\IdentifiedController($this->app), 'identifiedId');

        $this->app['rest']->addConfig(__DIR__ . '/Resources/config/users_scheme.yml');
    }

    public function registerConsole($console)
    {
        new \Users\Console\UsersCommand($console);
    }
}
