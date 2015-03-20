<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueDocument extends Constraint
{
    public $message = 'Такая запись уже существует в коллекции';
    /**
     * Which mongo connection to use
     */
    public $connection = 'default';
    public $property;
    public $collection;

    public function getRequiredOptions()
    {
        return ['property', 'collection'];
    }

    public function getTargets()
    {
        return array(self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT);
    }
}
