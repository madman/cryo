<?php

namespace Users\Controller\Frontend;

use Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisterController extends Controller
{

    CONST EVENT_BUILDER_CREATED = 'register.builder.created';

    protected $form;

    protected function buildForm()
    {
        $this->form = $this->app['user.register.form.factory']->create();
    }

    public function actionRegister()
    {
        if ($this->app->security->isGranted(\Users\Roles::ROLE_USER)) {
            return new RedirectResponse('/');
        }

        $this->buildForm();

        if ($this->isPost()) {
            $result = $this->performRegister();

            if ($result) {
                return new RedirectResponse('/');
            }
        }

        return $this->render(
            'register',
            [
                'form' => $this->form->createView()
            ]
        );
    }

    public function actionRegisterJson()
    {
        if ($this->app->security->isGranted(\Users\Roles::ROLE_USER)) {
            return new JsonResponse(['redirect_to' => '/']);
        }

        $this->buildForm();
        $response = [];

        if ($this->isPost()) {
            $result = $this->performRegister();

            if ($result) {
                $response = ['success' => true];
            } else {
                $response = [
                    'success' => false,
                    'errors'  => (new \Core\Form\FormErrors)->getErrorMessages($this->form)
                ];
            }
        }

        return new JsonResponse($response);
    }

    protected function performRegister()
    {
        $this->form->handleRequest($this->app->request);

        if ($this->form->isValid()) {
            $ip = $this->app->request->getClientIp() ?: '127.0.0.1';

            $user = $this->form->getData();
            $user->setIp($ip);
            $user->register();
            $user->authenticate();

            return true;
        }
    }
}
