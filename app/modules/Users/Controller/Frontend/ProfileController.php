<?php
namespace Users\Controller\Frontend;

use Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Users\Component\PasswordChanger;
use Users\Form\ProfileForm;

class ProfileController extends Controller
{

    public function actionProfile()
    {
        if (!$this->app->security->isGranted(\Users\Roles::ROLE_USER)) {
            return new RedirectResponse($this->app->path('users/login'));
        }

        $user = $this->app->currentUser();

        $formType = new ProfileForm($this->app);
        $form     = $this->app['form.factory']->createBuilder($formType, $user)->getForm();

        if ($this->isPost()) {
            $form->handleRequest($this->app->request);

            if ($form->isValid()) {
                $changer           = new PasswordChanger();
                $changer->password = $this->app->request->get('profile_form')['new_password']['first'];

                $changer->change($user);

                $this->app->session->getFlashBag()->add(
                    'messages',
                    [
                        'type'    => 'success',
                        'message' => $this->app->trans('Ваш пароль был успешно изменен')
                    ]
                );

                return new RedirectResponse($this->app->request->getRequestUri());
            }
        }

        return $this->render(
            'profile',
            [
                'form' => $form->createView()
            ]
        );
    }
}
