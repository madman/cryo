<?php

namespace Core;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller
{

    /**
     * @var Application
     */
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    public function render($file, $data = [])
    {
        return (new ViewRenderer($this->app, $this->app['twig'], $this, $file, $data))->render();
    }

    public function isPost()
    {
        return $this->app->request->getMethod() === 'POST';
    }

    public function isAjax()
    {
        return $this->app->request->isXmlHttpRequest();
    }

    public function isGet()
    {
        return $this->app->request->getMethod() === 'GET';
    }

    public function refresh()
    {
        return $this->redirect($this->app->request->getRequestUri());
    }

    public function redirect($url)
    {
        return new RedirectResponse($url);
    }
}
