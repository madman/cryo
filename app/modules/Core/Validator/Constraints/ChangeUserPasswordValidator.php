<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Users\Entity\User;

/**
 * we validate if typed new password is equal to old one
 */
class ChangeUserPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $fakeUser = new User();
        $fakeUser->password = $value;
        $fakeUser->encodePassword();

        if ($fakeUser->getPassword() === $constraint->userPassword) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }
}