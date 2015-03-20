<?php

namespace Users\Entity;

use Core\ApplicationRegistry;
use Core\Db\Entity,
    Core\Validator\Constraints\UniqueDocument;
use Core\Db\Manager;
use Symfony\Component\Validator\Mapping\ClassMetadata,
    Symfony\Component\Validator\Constraints\NotBlank,
    Symfony\Component\Validator\Constraints\Email,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Users\UsersEvents,
    Users\Component\User as SymfonyUser;

/**
 * TODO:
 *  - Move remind_code to another collection. Add lifetime.
 *  - Add validator UserPassword validator and use it globally
 *    on Login, Register, Remind. Make this validator configurable.
 *  - Implement isAccountNonExpired, isAccountNonLocked, isCredentialsNonExpired,
 *    isEnabled.
 */
class User extends Entity implements UserInterface
{
    /**
     * @Field
     */
    protected $active;

    /**
     * @Field
     */
    protected $email;

    /**
     * @Field
     */
    protected $email_verified;

    /**
     * @Field
     */
    protected $level;

    /**
     * @Field
     */
    protected $status;

    /**
     * @Field
     */
    protected $login;

    /**
     * @Field
     */
    protected $ip;

    /**
     * @Field
     */
    protected $password;

    /**
     * @Field
     */
    protected $roles = [];

    /**
     * @Field
     */
    protected $currency;

    /**
     * @Field
     */
    protected $phones = [];

    /**
     * @Field
     */
    protected $comments;

    /**
     * @Field
     */
    protected $birthday;

    /**
     * @Field
     */
    protected $gender;

    /**
     * @Field
     *
     * Can use this properties [
     *  payouts
     *  payouts_amount
     *  deposits
     *  deposits_amount
     *  rounds
     *  bets
     *  wins
     * ]
     */
    protected $financial;

    /**
     * @Field
     */
    protected $lastname;

    /**
     * @Field
     */
    protected $firstname;

    /**
     * @Field
     */
    protected $middlename;

    /**
     * @Field
     */
    protected $nickname;

    /**
     * @Field
     */
    protected $address;

    /**
     * @Field
     */
    protected $country;

    /**
     * @Field
     */
    protected $is_test;

    /**
     * @Field
     */
    protected $identified;

    /**
     * @Field
     * This column is compounded of social network name and uid
     * to search user on one column instead of two.
     */
    protected $social_identity;

    /**
     * @Field
     * Detailed user social data
     */
    protected $social_data;

    /**
     * @Field
     * Stores temp code that helps to find user
     * on password remind
     */
    protected $remind_code;

    /**
     * @Field
     * qs cookie value on user register
     */
    protected $partner_id;

    /**
     * @Field
     */
    protected $created_at;

    /**
     * @Field
     */
    protected $updated_at;

    /**
     * @Field
     */
    protected $last_action_at;

    /**
     * @Field
     */
    protected $verified;

    /**
     * @Field
     */
    protected $is_active;

    /**
     * @Field
     */
    protected $bonus_offers;

    /**
     * @Field
     */
    protected $portraits;

    /**
     * @Field this is random string generated on user register.
     * Used, for example, in websockets to hide user id.
     */
    protected $secret_hash;

    /**
     * @Field
     * User custom fields.
     * Usage:
     * <pre>
     *      $user->setCustom('name', 'value');
     *      $user->save();
     *
     *      echo $user->getCustom('name'); // value
     *      echo $user->getCustom('not-exists'); // null
     *      echo $user->getCustom('not-exists', 'default'); // default
     * </pre>
     */
    protected $custom_fields = [];

    protected $extra_data = [];

    protected $old_properties = [];

    public function __construct($properties = [])
    {
        parent::populateProperties($properties);
        $this->old_properties = $properties;
    }

    /**
     * Validation rules
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('email', new NotBlank());
        $metadata->addPropertyConstraint('email', new Email());
        $metadata->addConstraint(
            new UniqueDocument(
                [
                    'collection' => 'users',
                    'property'   => 'email',
                    'message'    => 'Такой email уже занят'
                ]
            )
        );
        $metadata->addPropertyConstraint('password', new NotBlank());
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add new role to user.
     * Note, you must call save manually.
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
        $this->roles   = array_unique($this->roles);
    }

    /**
     * Removes role
     */
    public function rmRole($role)
    {
        if (in_array($role, $this->roles)) {
            array_splice($this->roles, array_search($role, $this->roles), 1);
        }
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Username is email
     */
    public function setUsername($username)
    {
        $this->email = $username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
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
        return $this->getApp()->config->get('security/password_salt');
    }

    public function register()
    {
        $this->getApp()->dispatch(UsersEvents::NEW_USER_BEFORE_REGISTER, $this);

        if (empty($this->login)) {
            $this->login = $this->email;
        }

        $this->encodePassword();
        $this->save();

        $this->getApp()->dispatch(UsersEvents::NEW_USER_REGISTERED, $this);
    }

    /**
     * Authenticate user in current application request context.
     */
    public function authenticate()
    {
        $user  = new SymfonyUser($this->getUsername(), $this->getPassword(), $this->getRoles(), true, true, true);
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'default', $user->getRoles());
        $this->app->security->setToken($token);
        $this->app->session->set('_security_default', serialize($token));
        $this->app->dispatch(UsersEvents::USER_AUTHORIZATION, $this);
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
        $encoder        = $this->app['security.encoder_factory']->getEncoder($this);
        $this->password = $encoder->encodePassword($this->password, $this->getSalt());
    }

    public function beforeSave()
    {
        // Add default role for new users.
        if ($this->isNew() && empty($this->roles)) {
            $this->addRole(\Users\Roles::ROLE_USER);
        }

        if ($this->isNew()) {
            $this->created_at  = new \MongoDate;
            $this->secret_hash = sha1($this->email . $this->password . time() . $this->getSalt());
        }

        $this->updated_at = new \MongoDate;

        // Track partner_id change.
        if (!$this->isNew() && isset($this->old_properties['partner_id']) && $this->old_properties['partner_id'] !== $this->partner_id) {
            throw new \Exception('User partner_id should not be changed');
        }
    }

    public function loadExtraData()
    {
        $this->extra_data = UserExtraData::manager()->findOneByUserId($this->getId());
        if (!$this->extra_data) {
            $this->extra_data = new UserExtraData;
            $this->extra_data->setUserId($this->getId());
        }
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function addIdentified($identified)
    {
        $this->identified[] = $identified;
    }

    public function removeIdentified($identifiedId)
    {
        foreach ($this->identified as $key => $value) {
            if ($value['id'] == $identifiedId) {
                unset($this->identified[$key]);
            }
        }

        $this->identified = array_values($this->identified);
    }

    public function updateIdentified($identifiedId, $data)
    {
        foreach ($this->identified as &$identified) {
            if ($identified['id'] == $identifiedId) {
                foreach ($data as $key => $value) {
                    $identified[$key] = $value;
                }
            }
        }
    }

    public function getIdentified($identifiedId = null)
    {
        if ($identifiedId) {
            foreach ($this->identified as $value) {
                if ($value['id'] == $identifiedId) {
                    return $value;
                }
            }

            return [];
        }

        return $this->identified;
    }

    public function addPhone($phone)
    {
        $this->phones[] = $phone;
    }

    public function removePhone($phoneId)
    {
        foreach ($this->phones as $key => $value) {
            if ($value['id'] == $phoneId) {
                unset($this->phones[$key]);
            }
        }

        $this->phones = array_values($this->phones);
    }

    public function updatePhone($phoneId, $data)
    {
        foreach ($this->phones as &$phone) {
            if ($phone['id'] == $phoneId) {
                foreach ($data as $key => $value) {
                    $phone[$key] = $value;
                }
            }
        }
    }

    public function getPhones($phoneId = null)
    {
        if ($phoneId) {
            foreach ($this->phones as $value) {
                if ($value['id'] == $phoneId) {
                    return $value;
                }
            }

            return [];
        }

        return $this->phones;
    }

    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }

    public function removeComment($commentId)
    {
        foreach ($this->comments as $key => $value) {
            if ($value['id'] == $commentId) {
                unset($this->comments[$key]);
            }
        }

        $this->comments = array_values($this->comments);
    }

    public function updateComment($commentId, $data)
    {
        foreach ($this->comments as &$comment) {
            if ($comment['id'] == $commentId) {
                foreach ($data as $key => $value) {
                    $comment[$key] = $value;
                }
            }
        }
    }

    public function getComments($commentId = null)
    {
        if ($commentId) {
            foreach ($this->comments as $value) {
                if ($value['id'] == $commentId) {
                    return $value;
                }
            }

            return [];
        }

        return $this->comments;
    }

    public function addBonusOffer($offer)
    {
        $this->bonus_offers[] = $offer;
    }

    public function removeBonusOffer($offerId)
    {
        foreach ($this->bonus_offers as $key => $value) {
            if ($value['id'] == $offerId) {
                unset($this->bonus_offers[$key]);
            }
        }

        $this->bonus_offers = array_values($this->bonus_offers);
    }

    public function updateBonusOffer($offerId, $data)
    {
        foreach ($this->bonus_offers as &$offer) {
            if ($offer['id'] == $offerId) {
                foreach ($data as $key => $value) {
                    $offer[$key] = $value;
                }
            }
        }
    }

    public function getBonusOffers($offerId = null)
    {
        if ($offerId) {
            foreach ($this->bonus_offers as $value) {
                if ($value['id'] == $offerId) {
                    return $value;
                }
            }

            return [];
        }

        return $this->bonus_offers;
    }

    public function addPortrait($portrait)
    {
        $this->portraits[] = $portrait;
    }

    public function removePortrait($portraitId)
    {
        foreach ($this->portraits as $key => $value) {
            if ($value['id'] == $portraitId) {
                unset($this->portraits[$key]);
            }
        }

        $this->portraits = array_values($this->portraits);
    }

    public function updatePortrait($portraitId, $data)
    {
        foreach ($this->portraits as &$portrait) {
            if ($portrait['id'] == $portraitId) {
                foreach ($data as $key => $value) {
                    $portrait[$key] = $value;
                }
            }
        }
    }

    public function getPortraits($portraitId = null)
    {
        if ($portraitId) {
            foreach ($this->portraits as $value) {
                if ($value['id'] == $portraitId) {
                    return $value;
                }
            }

            return [];
        }

        return $this->portraits;
    }

    public function incFinancial($key, $value)
    {
        if (!is_array($this->financial)) {
            $this->financial = [];
        }

        if (!isset($this->financial[$key])) {
            $this->financial[$key] = 0;
        }

        $this->financial[$key] += $value;
    }

    /**
     * Set user financial data
     *
     * @param $financial array with keys [
     *  payouts
     *  payouts_amount
     *  deposits
     *  deposits_amount
     *  rounds
     *  bets
     *  wins
     * ]
     */
    public function setFinancial($financial)
    {
        $this->financial = $financial;
    }

    public function setCustom($key, $value)
    {
        if (!is_array($this->custom_fields)) {
            $this->custom_fields = [];
        }
        $this->custom_fields[$key] = $value;
    }

    public function getCustom($key, $default = null)
    {
        if (isset($this->custom_fields[$key])) {
            return $this->custom_fields[$key];
        }

        return $default;
    }

    public function removeCustom($key)
    {
        if (isset($this->custom_fields[$key])) {
            unset($this->custom_fields[$key]);
        }
    }

    public function setGender($gender)
    {
        if (in_array($gender, ['M', 'W'])) {
            $this->gender = $gender;
        } else {
            $this->gender = null;
        }
    }

    /**
     * @return UserManager
     */
    public static function manager()
    {
        return parent::manager();
    }
}
