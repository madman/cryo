<?php
namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CurrentUserPassword extends Constraint
{
    public $message = 'Текущий пароль не совпадает с вашим паролем';
    public $userPassword;

    public function getRequiredOptions()
    {
        return ['userPassword'];
    }
}
