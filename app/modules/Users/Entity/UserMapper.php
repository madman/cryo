<?php

namespace Users\Entity;

use Core\Db\Mapper;
use Core\Db\EntityNotFoundException;

class UserMapper extends Mapper
{

    public function findById($id)
    {
        if ($data = $this->getConnection()->fetchAssoc("SELECT * FROM users where id = :id", ['id' => $id])) {
            return new User($data);
        }

        throw new EntityNotFoundException(sprintf('Користувача #%s не знайдено', $id));
    }

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

    public function remove(User $user) {
        return $this->getConnection()->delete('users', array('id' => $user->getId()));
    }

    public function save(User $user) {
        if ($user->isNew()) {
            return $this->getConnection()->insert('users', $user->extract());
        } else {
            $data = $user->extract();
            unset($data['id']);
            return $this->getConnection()->update('users', $data, ['id' => $user->getId()]);
        }
    }
}
