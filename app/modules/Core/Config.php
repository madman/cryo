<?php

namespace Core;

use Symfony\Component\Yaml\Parser;

/**
 * Class to work with yml config files.
 */
class Config
{

    protected $configPath;
    protected $config = [];

    public function __construct($configPath)
    {
        $this->parser     = new Parser;
        $this->configPath = $configPath;

        $this->merge($configPath);
    }

    /**
     * Retreive value by its key
     *
     * <pre>
     * $config->get('debug'); // true
     * $config-get('options'); // array
     *
     * // Also, you may use "path" to retreive nested options.
     * $config->get('options/host'); // localhost
     * </pre>
     */
    public function get($key)
    {
        $path = explode('/', $key);

        if (sizeof($path) > 1) {
            return $this->fetchNested($path);
        } else {
            if (isset($this->config[$path[0]])) {
                return $this->config[$path[0]];
            }
        }
    }

    /**
     * Merge new config into current
     */
    public function merge($file)
    {
        $result = $this->parse($file);

        if (is_array($result)) {
            $this->config = array_replace_recursive($this->config, $result);
        }
    }

    /**
     * Returns all config params.
     */
    public function dump()
    {
        return $this->config;
    }

    /**
     * Find option in config by path.
     *
     * @param array $path . E.g: ['level0', 'level1', 'value']
     */
    protected function fetchNested($path)
    {
        $value = $this->config;
        $size  = sizeof($path);

        for ($i = 0; $i < $size; ++$i) {
            if (isset($value[$path[$i]])) {
                $value = $value[$path[$i]];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Reads yml config and returns array
     */
    protected function parse($file)
    {
        return $this->parser->parse(file_get_contents($file));
    }

}
