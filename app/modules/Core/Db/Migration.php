<?php

namespace Core\Db;

class Migration
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function up()
    {
        throw new \Exception('You should implement "up" method');
    }
}
