<?php

namespace Core\Db;
use Doctrine\DBAL\Connection;

abstract class Mapper
{
    /**
     *
     * @var Doctrine\DBAL\Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    protected function getConnection() {
        return $this->connection;
    }
}
