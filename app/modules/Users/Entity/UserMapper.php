<?php

namespace Users\Entity;

use Core\Db\Mapper;
use Core\Db\EntityNotFoundException;

class UserMapper extends Mapper
{
    public function findByUsername($username)
    {
        if ($data = $this->getConnection()->fetchAssoc("SELECT * FROM users where username = :username", ['username' => $username])) {
            return new User($data);
        }

        throw new EntityNotFoundException(sprintf('Користувача "%s" не знайдено', $username));
    }
    
    public function findAll() {
        return $this->getConnection()->fetchAll("SELECT * FROM users");
    }
}
