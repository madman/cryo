<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Users\Entity\User;

/**
 * we validate if typed password equal to existed one
 * Class SimpleCurrentUserPasswordValidator
 * @package Core\Validator\Constraints
 */
class CurrentUserPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $fakeUser = new User();
        $fakeUser->password = $value;
        $fakeUser->encodePassword();

        if ($fakeUser->getPassword() !== $constraint->userPassword) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }
}