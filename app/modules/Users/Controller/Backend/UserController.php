<?php

namespace Users\Controller\Backend;

use Compoints\Entity\Personal;
use Core\Controller;
use Users\Form\SearchForm;
use Core\Db\EntityNotFoundException;
use Users\Entity\User;

class UserController extends Controller
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchQuery = $this->app->request->get('search');
        $currentPage = $this->app->request->get('p', 1);

        list($users, $pagesNumber) = User::manager()->search($searchQuery, $currentPage);

        return $this->render(
            'index',
            [
                'users'       => $users,
                'searchQuery' => $searchQuery,
                'currentPage' => $currentPage,
                'pagesNumber' => $pagesNumber,
                'token'       => $this->app['form.csrf_provider']->generateCsrfToken('ajax-protection')
            ]
        );
    }

    /**
     * @param $id
     *
     * @return string|void
     */
    public function actionView($id)
    {
        /** @var \Users\Entity\User $user */
        $user = User::manager()->findById($id);

        if (!$user) {
            throw new EntityNotFoundException($this->app->trans('Страница не найдена'));
        }

        $balance  = $this->app['user.balance.factory']($user->getId());
        $points   = $this->app['user.compoints.factory']($user->getId());
        $level    = $this->app['compoints.level']->forUser($user)->getLevel();
        $bonus    = $this->app['bonus.balance.helper.factory']($user);
        $personal = Personal::manager()->findAllByUser($user->getId());

        $l = $this->app['user.compoints.factory']($user->getId());

        return $this->render(
            'view',
            [
                'model'         => $user,
                'balance'       => $balance,
                'points'        => $points,
                'level'         => $level,
                'personal'      => $personal,
                'bonus'         => $bonus,
                'compoints_log' => $l->getUserLog((time() - 3600 * 24 * 1), time())->limit(100),
            ]
        );
    }
}
