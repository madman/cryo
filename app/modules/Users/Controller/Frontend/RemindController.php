<?php

namespace Users\Controller\Frontend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Core\Controller;
use Users\Entity\User,
    Users\Form\EnterNewPasswordForm,
    Users\Form\RemindForm,
    Users\Component\PasswordChanger,
    Users\Component\RemindLinkSender;

class RemindController extends Controller
{
    public function actionRemind()
    {
        $formType = new RemindForm($this->app);
        $sender   = new RemindLinkSender($this->app);

        $form = $this->app['form.factory']->createBuilder($formType, $sender)->getForm();

        if ($this->isPost()) {
            $form->handleRequest($this->app->request);

            if ($form->isValid()) {
                $sender->send();
                $this->app->session->getFlashBag()->add('email-has-been-sent', true);

                if ($this->isAjax()) {
                    return new JsonResponse(
                        [
                            'success' => true
                        ]
                    );
                } else {
                    return $this->refresh();
                }
            } else {
                if ($this->isAjax()) {
                    return new JsonResponse(
                        [
                            'success' => false,
                            'errors'  => (new \Core\Form\FormErrors)->getErrorMessages($form)
                        ]
                    );
                }
            }
        }

        return $this->render(
            'remind',
            [
                'form' => $form->createView()
            ]
        );
    }

    public function actionEnterNewPassword($code)
    {
        $user = User::manager()->findByRemindCode($code);

        if (!$user) {
            throw new \Exception('Remind code not found');
        }

        $formType = new EnterNewPasswordForm;
        $changer  = new PasswordChanger;

        $form = $this->app['form.factory']->createBuilder($formType, $changer)->getForm();

        if ($this->isPost()) {
            $form->handleRequest($this->app->request);

            if ($form->isValid()) {
                $changer->change($user);
                $this->app->session->getFlashBag()->add('password-has-been-changed', true);

                if ($this->isAjax()) {
                    return new JsonResponse(
                        [
                            'success' => true
                        ]
                    );
                } else {
                    return new RedirectResponse($this->app->path('users/login'));
                }
            } else {
                if ($this->isAjax()) {
                    return new JsonResponse(
                        [
                            'success' => false,
                            'errors'  => (new \Core\Form\FormErrors)->getErrorMessages($form)
                        ]
                    );
                }
            }
        }

        return $this->render(
            'enter_new_password',
            [
                'form' => $form->createView()
            ]
        );
    }

}
