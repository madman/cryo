<?php
namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ChangeUserPassword extends Constraint
{
    public $message = 'Новый пароль не должен совпадать со старым';
    public $userPassword;

    public function getRequiredOptions()
    {
        return ['userPassword'];
    }
}
