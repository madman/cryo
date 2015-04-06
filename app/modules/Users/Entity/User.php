<?php

namespace Users\Entity;

use Core\ApplicationRegistry;
use Core\Db\Entity;
use Core\Db\Manager;
use Symfony\Component\Validator\Mapping\ClassMetadata,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Users\UsersEvents,
    Users\Component\User as SymfonyUser;

class User extends Entity
{
    protected $app;
    
      public function __construct($data = array()) {
          parent::__construct($data);
          
          $this->app = ApplicationRegistry::get();
      }
    
    protected $id = null;
    protected $username;
    protected $password;
    
    public function extract() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
    public function hydrate($data) {
        foreach ($data as $property=>$value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->app['config']->get('security/password_salt');
    }

    /**
     * Authenticate user in current application request context.
     */
    public function authenticate()
    {
        $user  = new SymfonyUser($this->username, $this->password, ['ROLE_USER']);
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'default', ['ROLE_USER']);
        $this->app->security->setToken($token);
        $this->app->session->set('_security_default', serialize($token));
    }

    /**
     * Encodes plain-text password.
     * Usage:
     * <pre>
     *      $user->password = 'secret';
     *      $user->encodePassword();
     *      echo $user->password; // -> hash
     * </pre>
     */
    public function encodePassword()
    {
        $encoder        = $this->app['security.encoder_factory']->getEncoder(new SymfonyUser($this->username, $this->password, ['ROLE_USER']));
        $this->password = $encoder->encodePassword($this->password, $this->getSalt());
    }
}
