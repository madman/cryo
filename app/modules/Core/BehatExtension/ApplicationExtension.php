<?php

namespace Core\BehatExtension;

use Behat\Testwork\ServiceContainer\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Behat\Testwork\ServiceContainer\ExtensionManager;

/**
 * Adds ability to access full application env from tests.
 */
class SilexApplicationInitializer implements \Behat\Behat\Context\Initializer\ContextInitializer
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function initializeContext(\Behat\Behat\Context\Context $context)
    {
        if (method_exists($context, 'setApplication')) {
            $context->setApplication($this->loadApp());
        }
    }

    private function loadApp()
    {
        $_SERVER['APP_ENV'] = $this->parameters['env'];
        $app = require __DIR__ . '/../../../bootstrap.php';

        return $app;
    }
}

class ApplicationExtension implements Extension
{

    private $config;

    public function process(ContainerBuilder $container)
    {
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'application_extension';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('env')->defaultNull()->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = $container->register('silex_app_initializer', '\Core\BehatExtension\SilexApplicationInitializer', array($config));
        $definition->setArguments(array($config));
        $definition->addTag('context.initializer', array('priority' => 50));
    }
}
