<?php

namespace Users\Entity;

use Core\Db\Mapper;

class UserMapper extends Mapper
{
    public function findByUsername($username)
    {
        return $this->getConnection()->fetchAssoc("SELECT * FROM users where username = :username", ['username' => $username]);
    }
    
    public function findAll() {
        return $this->getConnection()->fetchAll("SELECT * FROM users");
    }
}
