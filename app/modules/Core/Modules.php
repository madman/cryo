<?php

namespace Core;

use Symfony\Component\Finder\Finder;

/**
 * Performs finding and bootstraping modules.
 *
 * Examples:
 * <pre>
 *      $app['modules']->locate('/path/to/many/modules/dir');
 * </pre>
 */
class Modules
{

    private $loaded = [];
    private $exclude = [];

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Finds Module.php files in specified directory.
     *
     * @param string $dir fullpath to modules dir
     */
    public function locate($dir)
    {
        $finder = new Finder;

        $finder->in($dir)
            ->depth(1)
            ->followLinks()
            ->name('Module.php');

        $this->load($finder);
    }

    /**
     * Returns instance of loadaed Module class.
     */
    public function getModule($name)
    {
        if ($this->isModuleLoaded($name)) {
            return $this->loaded[$name];
        } else {
            throw new \Exception("Module $name not found");
        }
    }

    public function getModules()
    {
        return $this->loaded;
    }

    public function isModuleLoaded($name)
    {
        if (isset($this->loaded[$name])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $names
     */
    public function exclude($names)
    {
        foreach ($names as $name) {
            $this->exclude[] = $name;
        }
    }

    public function isExcluded($name)
    {
        return in_array($name, $this->exclude);
    }

    /**
     * Loads module boot files.
     */
    protected function load($files)
    {
        foreach ($files as $name => $file) {
            $namespace = $file->getRelativePath();

            if (!$this->isModuleLoaded($namespace) && !$this->isExcluded($namespace)) {
                require_once $file->getPathname();

                $class  = $namespace . '\Module';
                $module = new $class;
                $module->initialize($this->app);

                $this->loaded[$namespace] = $module;
            }
        }
    }

}
