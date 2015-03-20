<?php

namespace Core\Interfaces;

use Core\Application;
use Admin\Interfaces\AdminMenuManagerInterface;

interface ModuleInterface
{
    public function initialize(Application $app);

    public function registerMenuItems(AdminMenuManagerInterface $manager);
}
