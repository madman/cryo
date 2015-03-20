<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Core\ApplicationRegistry;

class UniqueDocumentValidator extends ConstraintValidator
{

    public function validate($object, Constraint $constraint)
    {
        $app = ApplicationRegistry::get();

        $key   = $constraint->property;
        $value = $object->{$key};

        $query = [$key => $value];

        if ($object->_id instanceof \MongoId) {
            $query['_id'] = ['$ne' => $object->_id];
        }

        $count = $app['mongo.' . $constraint->connection]->{$constraint->collection}->count($query);

        if ($count > 0) {
            $this->context->addViolationAt($key, $constraint->message);
        }
    }

}

