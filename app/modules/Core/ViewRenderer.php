<?php

namespace Core;

/**
 * Helps to render controller view files.
 */
class ViewRenderer
{

    /**
     * @param mixed  $controller object or string
     * @param string $file       name to render
     */
    public function __construct($app, $twig, $controller, $file, $data = [])
    {
        $controllerObj = $controller;

        if (!is_string($controller)) {
            $controller = (new \ReflectionClass($controller))->getName();
        }

        $this->app        = $app;
        $this->twig       = $twig;
        $this->controller = $controller;
        $this->file       = $file;
        $this->data       = $data;

        if (!isset($this->data['app'])) {
            $this->data['app'] = $app;
            $this->data['this'] = $controllerObj;
        }
    }

    public function getPath()
    {
        $parts  = explode('\\', $this->controller);
        $module = $parts[0];

        // Remove 1 index. By convention it must be a "Controller" word.
        $parts[1] = '';

        // Remove 'Controller' postfix from class name.
        $last         = sizeof($parts) - 1;
        $parts[$last] = str_replace('Controller', '', $parts[$last]);

        $path = $this->build($parts);

        // First, we try to find view file in app/views dir
        if ($this->twig->getLoader()->exists($path)) {
            return $path;
        }

        // Else, add Resouces/views to path, and try to locate view
        // in modules directory.
        $parts[1] = 'Resources\views';

        return $this->build($parts);
    }

    public function render()
    {
        if ($this->isAjaxRequest()) {
            return $this->renderBlock();
        } else {
            return $this->renderFile();
        }
    }

    protected function renderFile()
    {
        return $this->twig->render($this->getPath(), $this->data);
    }

    protected function renderBlock($block='content')
    {
        $template = $this->twig->loadTemplate($this->getPath());

        if (false === array_key_exists($block, $template->getBlocks()))
            throw new \Exception('Block ' . $block . ' is not present in file ' . $this->getPath());

        return $template->renderBlock($block, $this->data);
    }

    protected function isAjaxRequest()
    {
        return $this->app->request->isXmlHttpRequest();
    }
    /**
     * Builds path to view file
     */
    protected function build($parts)
    {
        return implode('\\', $parts) . '\\' . $this->file . '.twig';
    }
}
