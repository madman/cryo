<?php

namespace Users\Component;

use Symfony\Component\Security\Core\User\UserInterface;

class PasswordChanger
{
    public $password;
    protected $encoder;

    public function change(UserInterface $user)
    {
        $user->password = $this->password;
        $user->encodePassword();
        $user->setRemindCode('');
        $user->save();
    }
}
