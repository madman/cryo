<?php

namespace Core;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler as OriginalAuthenticationFailureHandler,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\HttpFoundation\JsonResponse;

use Users\UsersEvents;

class DefaultAuthenticationFailureHandler extends OriginalAuthenticationFailureHandler
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $result = parent::onAuthenticationFailure($request, $exception);

        $app = ApplicationRegistry::get();
        $app->dispatch(UsersEvents::USER_AUTHORIZATION_FAIL, $exception);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false
            ]);
        }

        return $result;
    }
}
