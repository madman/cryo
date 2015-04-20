<?php

namespace Storage\Entity;

use Core\Db\Mapper;
use Core\Db\EntityNotFoundException;

class BloodMapper extends Mapper
{
    public function findById($id)
    {
        if ($data = $this->getConnection()->fetchAssoc("SELECT * FROM bloods where id = :id", ['id' => $id])) {
            return new Blood($data);
        }

        throw new EntityNotFoundException(sprintf('Кров "%s" не знайдено', $id));
    }
    
    public function findAll() {
        return $this->getConnection()->fetchAll("SELECT * FROM bloods");
    }
}