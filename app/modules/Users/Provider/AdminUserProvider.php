<?php

namespace Users\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Users\Entity\User as CoreUser,
    Users\Component\User;

class AdminUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = CoreUser::manager()->findOne(
            [
                'email' => $username,
                'roles' => ['$in' => [\Users\Roles::ROLE_ADMIN]]
            ]
        );

        if ($user) {
            return new User($user->getUsername(), $user->getPassword(), $user->getRoles(), true, true, true);
        }

        throw new UsernameNotFoundException;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Users\Component\User';
    }
}
