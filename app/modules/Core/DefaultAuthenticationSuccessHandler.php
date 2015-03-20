<?php

namespace Core;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler as OriginalAuthenticationHandler,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\HttpFoundation\JsonResponse;
use Users\UsersEvents;

class DefaultAuthenticationSuccessHandler extends OriginalAuthenticationHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $app = ApplicationRegistry::get();
        $app->dispatch(UsersEvents::USER_AUTHORIZATION, $app->currentUser());

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success'     => true,
                'redirect_to' => $this->determineTargetUrl($request)
            ]);
        }

        return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
    }
}
