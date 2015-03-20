<?php

namespace Users\Controller\Frontend;

use Core\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public function actionLogin()
    {
        if ($this->app->security->isGranted(\Users\Roles::ROLE_USER)) {
            if ($this->app->request->isXmlHttpRequest()) {
                return $this->app->json(['redirect' => true]);
            }

            return new RedirectResponse($this->app->path('startpage/index'));
        }

        return $this->render(
            'login',
            array(
                'error'         => $this->app['security.last_error']($this->app->request),
                'last_username' => $this->app['session']->get('_security.last_username'),
            )
        );
    }

    public function actionSecured()
    {
        if ($this->app->security->isGranted(\Users\Roles::ROLE_USER)) {
            return $this->app->trans('Hello') . ' ' . $this->app->user()->getUsername();
        } else {
            throw new AccessDeniedException;
        }
    }
}
