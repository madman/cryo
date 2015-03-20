<?php

namespace Users\Entity;

use Core\Db\Manager;

class UserExtraDataManager extends Manager
{
    protected $collection = 'users_extra_data';

    public function findOneByUserId($userId)
    {
        return $this->findOne(['user_id' => $userId]);

    }
}