<?php

namespace Core;

use Core\Traits\TranslatorTrait;
use Silex;
use Users;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;
use Symfony\Component\EventDispatcher\GenericEvent;
use Monolog\Logger;

class Application extends Silex\Application
{
    use Silex\Application\MonologTrait;
    use Silex\Application\UrlGeneratorTrait;
    use Silex\Application\SecurityTrait;

    public function initialize()
    {
        ApplicationRegistry::set($this);

        $this->initEnvironment();
        $this->initConfig();
        $this->initLogging();
        $this->initErrorHandling();
        $this->initCaching();
        //$this->initRedis();
        $this->initMongo();
        $this->initLocale();
        $this->initSession();
        $this->initProviders();
        $this->initDebugToolbar();
    }

    protected function initEnvironment()
    {
        $this->register(new Provider\EnvironmentDetectorProvider);
    }

    protected function initConfig()
    {
        $this->register(new Provider\ConfigProvider(CORE_CONFIG_DIR . '/config.yml'));

        $envConfig = CORE_CONFIG_DIR . '/' . $this['env']->getEnvironment() . '.yml';

        if (file_exists($envConfig)) {
            $this['config']->merge($envConfig);
        }

        date_default_timezone_set($this['config']->get('timezone'));
    }

    protected function initErrorHandling()
    {
        $this['debug'] = $this['config']->get('debug');
        $this->register(new Provider\ErrorHandlerProvider());
    }

    protected function initLogging()
    {
        $this->register(new Silex\Provider\MonologServiceProvider(), [
            'monolog.logfile' => CORE_RUNTIME_DIR . '/logs/' . $this['env']->getEnvironment() . '.log',
        ]);

        $this['monolog.level'] = function () {
            if ($level = $this->config->get('monolog/level')) {
                return $level;
            }

            return Logger::DEBUG;
        };
    }

    protected function initCaching()
    {
        $this['cache'] = $this->share(function () {
            $memcached = new \Memcached;

            $memcached->setOption(\Memcached::OPT_CONNECT_TIMEOUT, 100); // the timeout after which a server is considered DEAD, ms
            $memcached->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT); // set it to consistent hashing. If one memcached node is dead, its keys (and only its keys) will be evenly distributed to other nodes. This is where the magic is done. This is really different from removing one server in your ->addServers() call.
            $memcached->setOption(\Memcached::OPT_REMOVE_FAILED_SERVERS, true); // set it to «true», to enable the removal of dead servers.
            $memcached->setOption(\Memcached::OPT_RETRY_TIMEOUT, 1); // the timeout after which a server is considered DEAD. As my servers are on the same LAN, ping is ~0.5ms, so 10ms is large enough to consider the server is DEAD. Note that you have to wait twice that time before a node is marked DEAD, so if it's 1000ms, your script will lock for 2 seconds before ignoring the DEAD server. That may affect your response times a lot, and that's why I've set it very low.

            $memcached->setOption(\Memcached::OPT_PREFIX_KEY, $this['config']->get('memcached/prefix'));

            foreach ($this['config']->get('memcached/servers') as $server) {
                $memcached->addServer($server['host'], $server['port']);
            }

            return $memcached;
        });
    }

    protected function initRedis()
    {
        if (is_array($this['config']->get('redis'))) {
            $config = [
                'predis.parameters' => $this['config']->get('redis/dsn'),
                'predis.options'    => $this['config']->get('redis/options')
            ];
        } else {
            $config = [
                'predis.parameters' => $this['config']->get('redis'),
                'predis.options'    => ['profile' => '2.8']
            ];
        }

        $this->register(new \Predis\Silex\ClientServiceProvider, $config);
    }

    protected function initMongo()
    {
        $this->register(new Provider\MongoProvider($this->config->get('mongo')));
    }

    protected function initLocale()
    {
        $this['locale'] = $this->config->get('locale');
    }

    public function initSession()
    {
        $this->register(new Silex\Provider\SessionServiceProvider);
        $this['session.storage.handler'] = $this->share(function () {
            $mongo = $this['mongo.client.default'];

            return new MongoDbSessionHandler($mongo, [
                'database'   => $this->config->get('mongo/default/db'),
                'collection' => 'session'
            ]);
        });
    }

    protected function initProviders()
    {
        $this->register(new Provider\SecurityProvider);
        //$this->register(new Provider\IpAccessLimiterProvider);
        $this->register(new Silex\Provider\UrlGeneratorServiceProvider);
        $this->register(new Silex\Provider\FormServiceProvider);
        //$this->register(new Silex\Provider\TranslationServiceProvider);
        $this->register(new Silex\Provider\ValidatorServiceProvider);
        $this->register(new Silex\Provider\ServiceControllerServiceProvider);
        //$this->register(new Provider\SentryProvider);
        //$this->register(new Provider\SettingsStorageProvider);
        //$this->register(new Provider\BlocksStorageProvider);
        $this->register(new Provider\RouterProvider);
        $this->register(new Provider\TwigProvider);
        //$this->register(new Provider\DeviceDetectorProvider);
        //$this->register(new Provider\CurrenciesProvider);
        //$this->register(new Provider\QueryStringService);
        //$this->register(new Provider\FileSystemProvider);
        //$this->register(new Provider\GlobotunesTransportServiceProvider);
        //$this->register(new \L10n\Provider\L10nServiceProvider);
        //$this->register(new \Rest\Provider\ApplicationServiceProvider);

        // This must be registered last
        $this->register(new Provider\ModulesProvider);
    }

    protected function initDebugToolbar()
    {
        if ($this['debug']) {
            // Register the Silex/Symfony web debug toolbar.
            $this->register(new Silex\Provider\WebProfilerServiceProvider, array(
                'profiler.cache_dir'    => CORE_RUNTIME_DIR . '/profiler',
                'profiler.mount_prefix' => '/_profiler', // this is the default
            ));

            $this['twig.loader.filesystem']->addPath(
                CORE_ROOT_DIR . '/vendor/symfony/web-profiler-bundle/Symfony/Bundle/WebProfilerBundle/Resources/views',
                'WebProfiler'
            );
        }
    }

    /**
     * Make event dispatcher access easier
     */
    public function dispatch($eventName, $sender, $data = [])
    {
        $event = new GenericEvent($sender, $data);
        $this['dispatcher']->dispatch($eventName, $event);

        return $event;
    }

    /**
     * Allows to access services as properties.
     * e.g: $app->settings instead of $app['settings']
     */
    public function __get($var)
    {
        if (isset($this[$var])) {
            return $this[$var];
        }

        return $this->$var;
    }
}
