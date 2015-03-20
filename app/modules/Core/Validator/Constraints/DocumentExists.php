<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class DocumentExists extends Constraint
{
    public $message = 'Запись не найдена';
    public $connection = 'default';
    public $property;
    public $collection;
}
