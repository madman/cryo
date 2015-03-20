<?php

namespace Core;

/**
 * Helps to detect application environment.
 *
 * By default env supports next environments: production, development, test.
 * But you can add your own and check with EnvironmentDetector::is('your-own-env');
 * Default env is "development"
 *
 * Examples:
 * <pre>
 *    // Env will be detected from $_SERVER['APP_ENV'] or $_ENV['APP_ENV'];
 *    $env = new EnvironmentDetector;
 * </pre>
 */
class EnvironmentDetector
{

    /**
     * @var string Use production mode by default.
     */
    protected $env = 'development';

    /**
     * @var string $_SERVER key name to detect env.
     */
    protected $serverKeyName = 'APP_ENV';

    public function __construct($resource = null)
    {
        if (isset($_SERVER[$this->serverKeyName])) {
            $this->setEnvironment($_SERVER[$this->serverKeyName]);
        } elseif (isset($_ENV[$this->serverKeyName])) {
            $this->setEnvironment($_ENV[$this->serverKeyName]);
        }
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    public function setEnvironment($env)
    {
        $this->env = $env;
    }

    public function isProduction()
    {
        return $this->is('production');
    }

    public function isDevelopment()
    {
        return $this->is('development');
    }

    public function isTest()
    {
        return $this->is('test');
    }

    public function is($env)
    {
        return $this->env === $env;
    }

}
