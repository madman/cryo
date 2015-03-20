<?php

namespace Core\BehatExtension;

use Behat\Testwork\ServiceContainer\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\DependencyInjection\Definition;

class MongoInitializer implements \Behat\Behat\Context\Initializer\ContextInitializer
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function initializeContext(\Behat\Behat\Context\Context $context)
    {
        if (get_class($context) === 'MongoContext') {
            $mongo = new \MongoClient($this->parameters['server']);
            $db    = $mongo->{$this->parameters['database']};

            $context->setMongo($db);
        }
    }
}

class MongoExtension implements Extension
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
        return 'mongo_extension';
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
            ->scalarNode('server')->defaultNull()->end()
            ->scalarNode('database')->defaultNull()->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = $container->register('custom_initializer', '\Core\BehatExtension\MongoInitializer', array($config));
        $definition->setArguments(array($config));
        $definition->addTag('context.initializer', array('priority' => 100));
    }
}
