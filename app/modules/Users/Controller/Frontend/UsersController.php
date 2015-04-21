<?php
namespace Users\Controller\Frontend;

use Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Users\Component\PasswordChanger;
use Users\Entity\User;
use Users\Form\UserForm;

class UsersController extends Controller
{

    public function actionEdit($id)
    {
        return $this->update($this->app['db.users']->findById($id));
    }

    public function actionCreate()
    {
        return $this->update(new User);
    }

    public function actionDelete($id)
    {
        $user = $this->app['db.users']->findById($id);
        $this->app['db.users']->remove($user);

        $this->app->session->getFlashBag()->add('messages', [
            'type'    => 'success',
            'message' => sprintf('Користувача %s видалено', $user->username),
        ]);

        return $this->redirect($this->app->path('users/list'));
    }

    public function actionList()
    {
        return $this->render('list', ['users' => $this->app['db.users']->findAll()]);
    }

    protected function update(User $user)
    {
        $formType = new UserForm();
        $form = $this->app['form.factory']->createBuilder($formType, $user)->getForm();

        if ($this->isPost()) {
            $form->handleRequest($this->app->request);

            if ($form->isValid()) {

                $user->encodePassword();
                $this->app['db.users']->save($user);

                $this->app->session->getFlashBag()->add('messages', [
                    'type'    => 'success',
                    'message' => 'Зміни збережено'
                ]);

                return $this->redirect($this->app->path('users/list'));
            }
        }

        return $this->render('edit', [
            'form' => $form->createView()
        ]);
    }
}
