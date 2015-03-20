<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Core\ApplicationRegistry;

class DocumentExistsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $app = ApplicationRegistry::get();

        $key   = $constraint->property;
        $count = $app['mongo.' . $constraint->connection]->{$constraint->collection}->count([$key => $value]);

        if (!$count) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }
}

