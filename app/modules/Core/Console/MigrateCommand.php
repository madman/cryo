<?php

namespace Core\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand
{
    protected $console;

    public function __construct($console)
    {
        $this->migrations = new \Core\Migrations($console->app, $console->app['mongo.default']->migrations);
        $this->app = $console->app;

        $console->register('migrate:new')
            ->setDescription('Display list of new available migrations')
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                $new = $this->migrations->getNew();

                if ($new) {
                    foreach ($new as $m) {
                        $output->writeln($m);
                    }
                } else {
                    $output->writeln('No new migrations found');
                }
            });

        $console->register('migrate:up')
            ->setDescription('Execute each new migration')
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                $output->writeln('Please, wait. Executing new migrations.');

                foreach ($this->migrations->up() as $m) {
                    $output->writeln($m);
                }
            });

        $console->register('migrate:create')
            ->setDescription('Create new migration')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Migration name a-z'
            )
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Name of the module to save migration file'
            )
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                $name       = 'm'.time().'_'.$input->getArgument('name');
                $moduleName = $input->getArgument('module');

                $tpl = "<?php

class $name extends \Core\Db\Migration
{
    public function up()
    {

    }
}";

                $module = $this->app['modules']->getModule($moduleName);
                $path = dirname((new \ReflectionClass($module))->getFileName()) . DIRECTORY_SEPARATOR . $this->migrations->getMigrationsDir();
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $fullFilePath = $path.DIRECTORY_SEPARATOR.$name.'.php';
                file_put_contents($fullFilePath, $tpl);
                $output->writeln("Created file $fullFilePath");
            });
    }
}
