<?php

namespace Pages\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PagesCommand
{
    protected $console;
    protected $app;

    public function __construct($console)
    {
        $this->console = $console;
        $this->app = $console->app;

        $console->register('pages:export')
            ->addArgument(
                'filepath',
                InputArgument::OPTIONAL,
                'Path to backup file'
            )
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {

                    $tmp_path = sys_get_temp_dir() . '/neocore_pages_export_' . time();
                    $tmp_path_uploads = $tmp_path . '/uploads';
                    $tmp_path_uploads_actions = $tmp_path_uploads . '/actions';
                    $tmp_path_uploads_news = $tmp_path_uploads . '/news';

                    @mkdir($tmp_path_uploads_actions, 0777, true);
                    @mkdir($tmp_path_uploads_news, 0777, true);

                    if (!file_exists($tmp_path_uploads_news)) {
                        throw new \Exception('An error occurred while creating a temps folders');
                    }

                    $collection = $console->app['mongo.default']->pages;
                    $result = [];
                    foreach ($collection->find() as $item) {
                        try {
                            self::copyImage($item, CORE_UPLOAD_DIR, $tmp_path_uploads);
                        } catch (\Exception $e){
                            $output->writeln('<comment>'.$e->getMessage().'</comment>');
                        }
                        $result[] = $item;
                        $output->writeln('<info>Page ' . $item['_id'] . ' saved +</info>');
                    }
                    file_put_contents($tmp_path . '/pages.data', serialize($result));

                    //final
                    $filepath = $input->getArgument('filepath');

                    if (!$filepath) {
                        @mkdir(CORE_RUNTIME_DIR . '/dump');
                        $filepath = CORE_RUNTIME_DIR . '/dump/pages_' . date('Y-m-d_H-i-s') . '.zip';
                    }

                    \Core\Archive::create($tmp_path, $filepath);
                    $output->writeln('<info>Ok - ' . realpath($filepath) . '</info>');
            });

        $console->register('pages:import')
            ->addArgument(
                'filepath',
                InputArgument::REQUIRED,
                'Path to backup file'
            )
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
                    $filepath = $input->getArgument('filepath');

                    $tmp_path = sys_get_temp_dir() . '/neocore_pages_export_' . time();
                    $tmp_path_uploads = $tmp_path . '/uploads';

                    @mkdir(CORE_UPLOAD_DIR . '/news/');
                    @mkdir(CORE_UPLOAD_DIR . '/actions/');

                    if (!file_exists(CORE_UPLOAD_DIR . '/news/')) {
                        throw new \Exception('An error occurred while creating a uploads folders');
                    }

                    \Core\Archive::extractTo($filepath, $tmp_path);

                    if(!file_exists($tmp_path . '/pages.data')){
                        throw new \Exception('Invalid file format');
                    }

                    $result = unserialize(file_get_contents($tmp_path . '/pages.data'));
                    $collection = $console->app['mongo.default']->pages;
                    foreach ($result as $item) {
                        try {
                            $collection->insert($item);
                            $output->writeln('<info>Page ' . $item['_id'] . ' - ok</info>');
                        } catch (\Exception $e) {
                            $output->writeln('<comment>Page ' . $item['_id'] . ' already exists</comment>');
                        }
                        try {
                            self::copyImage($item, $tmp_path_uploads, CORE_UPLOAD_DIR);
                        } catch (\Exception $e){
                            $output->writeln('<comment>'.$e->getMessage().'</comment>');
                        }
                    }

                    $output->writeln('<info>Ok</info>');
            });
    }

    public static function copyImage($item, $sourceDir, $destDir)
    {
        if (!isset($item['image'])) {
            throw new \Exception('Invalid entity format');
        }

        if ($item['type'] == 'news') {
            $sourceDir .= '/news';
            $destDir .= '/news';
        }
        if ($item['type'] == 'action') {
            $sourceDir .= '/actions';
            $destDir .= '/actions';
        }
        @copy($sourceDir . '/' . $item['image'], $destDir . '/' . $item['image']);
    }
}