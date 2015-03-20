<?php

namespace Core;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\Request;

class Router
{

    protected $app;
    protected $registeredRoutes;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function addRoutes($ymlFilePath)
    {
        $this->process($this->load($ymlFilePath));
    }

    /**
     * Load and parses yml file
     *
     * @param string $file full path to yml file.
     */
    public function load($file)
    {
        $parser = new Parser;

        return $parser->parse(file_get_contents($file));
    }

    /**
     * @param array $config full config array
     */
    public function process($config)
    {
        foreach ($config['routes'] as $controller => $routes) {
            $this->createRoute($controller, $routes, $config);
        }
    }

    public function getRegisteredRoutes()
    {
        return $this->registeredRoutes;
    }

    /**
     * TODO: Refactor. Optimize
     */
    public function createRoute($controller, $route_config, $config)
    {
        $config = array_merge(['module' => '', 'prefix' => ''], $config);

        // Respect controller options
        if (isset($route_config['options'])) {
            $options = $route_config['options'];
            unset($route_config['options']);

            foreach ($options as $key => $val) {
                $config[$key] = $val;
            }
        }

        foreach ($route_config as $action => $attributes) {
            if (!is_array($attributes)) {
                $attributes = ['url' => $attributes];
            }

            $fullUrl   = $config['prefix'] . $attributes['url'];
            $routeName = $config['module'] . '/' . $action;

            if (isset($attributes['action'])) {
                $action = $attributes['action'];
            }

            $params = [];
            if (isset($attributes['params'])) {
                $params = $attributes['params'];
            }

            $this->registeredRoutes[] = [
                'url'        => $fullUrl,
                'name'       => $routeName,
                'controller' => $controller,
                'action'     => $action,
            ];
            $route = $this->app->match($fullUrl, function (Request $r) use ($controller, $action, $params) {
                // TODO: Is there a better way to get named params?
                $namedParams = $r->attributes->all()['_route_params'];

                if (!empty($params) && is_array($params)) {
                    $namedParams = array_merge($params, $namedParams);
                }

                return $this->processRequest($controller, $action, $namedParams);
            })->bind($routeName);

            foreach (['value', 'assert'] as $name) {
                $this->callOnRoute($route, $name, $attributes);
            }
        }
    }

    /**
     * Main method. Used when silex catches named route.
     *
     * @param string $module
     * @param string $controller class name with namespace
     * @param string $action     method name without `action` prefix
     * @param array  $params
     */
    public function processRequest($controller, $action, $params = [])
    {
        $controller = new $controller($this->app);
        $action     = 'action' . ucfirst($action);

        if (method_exists($controller, 'initialize')) {
            $controller->initialize();
        }

        $sorted = $this->sortParams($controller, $action, $params);

        return call_user_func_array([$controller, $action], $sorted);
    }

    /**
     * Calls validation, assets on route object.
     */
    protected function callOnRoute($route, $name, $val)
    {
        if (isset($val[$name])) {
            foreach ($val[$name] as $key => $value) {
                $route->$name($key, $value);
            }
        }
    }

    protected function sortParams($controller, $action, $params)
    {
        $sortedParams = [];
        foreach ((new \ReflectionMethod($controller, $action))->getParameters() as $p) {
            if (isset($params[$p->getName()])) {
                $sortedParams[$p->getName()] = $params[$p->getName()];
            }
        }

        return $sortedParams;
    }

}
