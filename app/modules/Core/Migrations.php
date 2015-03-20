<?php

namespace Core;

use Symfony\Component\Finder\Finder;

class Migrations
{
    const FILE_PATTERN = '/^m(\d+)/';

    protected $app;

    /**
     * @var Core\Modules
     */
    protected $modules;

    /**
     * @var Symfony\Component\Finder\Finder $finder
     */
    protected $finder;

    /**
     * @var $collection MongoCollection with previously executed migrations.
     */
    protected $collection;

    /**
     * Dir name in module to store migrations
     */
    protected $migrationsDir = '/Resources/migrations';

    /**
     * @param Core\Modules $modules
     */
    public function __construct($app, $collection)
    {
        $this->app        = $app;
        $this->modules    = $app->modules;
        $this->collection = $collection;
        $this->finder     = new Finder;
    }

    /**
     * Get all modules dir with may contain migration files
     */
    public function getDirectories()
    {
        $result = [];

        foreach ($this->modules->getModules() as $module) {
            $reflection = new \ReflectionClass($module);
            $path       = realpath(dirname($reflection->getFileName())) . $this->migrationsDir;

            if (file_exists($path)) {
                $result[] = $path;
            }
        }

        return $result;
    }

    /**
     * Find migrations in directories
     */
    public function getMigrationFiles()
    {
        $files = [];

        foreach ($this->getDirectories() as $dir) {
            $result = $this->finder
                ->in($dir)
                ->files()
                ->name(self::FILE_PATTERN);

            foreach ($result as $f) {
                $files[] = $f->getRealPath();
            }
        }

        return $files;
    }

    public function getMigrationsDir()
    {
        return $this->migrationsDir;
    }

    public function getNew()
    {
        $result = [];

        foreach ($this->getMigrationFiles() as $file) {
            $migrationName = $this->extractClassName($file);

            if (!$this->isMigrationApplied($migrationName)) {
                $result[$migrationName] = $file;
            }
        }

        return $this->sort($result);
    }

    public function up()
    {
        $processed = [];

        foreach ($this->getNew() as $name => $file) {
            $migration = $this->buildMigrationClass($file);
            $migration->up();
            $this->markMigrationApplied($name);

            $processed[] = $name;
        }

        return $processed;
    }

    public function isMigrationApplied($migrationName)
    {
        return $this->collection->count(['name' => $migrationName]) > 0;
    }

    public function markMigrationApplied($migrationName)
    {
        $this->collection->insert([
            'name'     => $migrationName,
            'datetime' => new \MongoDate()
        ]);
    }

    public function extractClassName($file)
    {
        return basename($file, '.php');
    }

    protected function buildMigrationClass($file)
    {
        require_once $file;
        $class = $this->extractClassName($file);

        return new $class($this->app);
    }

    /**
     * Sort migrations by timestamp
     */
    public function sort($arr)
    {
        uksort($arr, function ($a, $b) {
            preg_match(self::FILE_PATTERN, $a, $am);
            preg_match(self::FILE_PATTERN, $b, $bm);

            return (int)$am[1] > $bm[1];
        });

        return $arr;
    }
}
