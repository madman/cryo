<?php

namespace Users\Traits;

use Users\Entity\User;

/**
 * Helps to access authrorized user model.
 *
 * <pre>
 *      $app->currentUser(); // will return Users\Entity\User
 * </pre>
 */
trait CurrentUserTrait
{
    private $_current_user;

    /**
     * @return User
     */
    public function currentUser()
    {
        if (null === $this->_current_user && $this->user()) {
            $this->_current_user = User::manager()->findByEmail($this->user()->getUsername());
        }

        return $this->_current_user;
    }
}
