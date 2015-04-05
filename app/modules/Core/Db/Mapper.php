<?php

namespace Core\Db;

abstract class Mapper
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    abstract public function find();
    abstract public function save();
    abstract public function load($id);
}
