<?php

namespace Users\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Users\Component\User;

class UserProvider implements UserProviderInterface
{
    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;
    
    public function __construct(\Doctrine\DBAL\Connection $connection) {
        $this->connection = $connection;
    }


    public function loadUserByUsername($username)
    {
        $user = $this->connection->fetchAssoc("SELECT * FROM users where username = :username", ['username' => $username]);

        if ($user) {

            return new User($user['username'], $user['password'], ['ROLE_USER']);
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
