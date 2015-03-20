<?php

namespace Users\Entity;

use Core\Db\Entity;

class UserExtraData extends Entity
{
    /**
     * @Field
     */
    protected $user_id;

    /**
     * @Field
     */
    protected $portrait;

    /**
     * @Field
     */
    protected $bonus_offer;

    /**
     * @Field
     */
    protected $bonus_enabled = 0;

    /**
     * @Field
     */
    protected $comment;

    public function set($name, $value, $author, $append)
    {
        $value = ['text' => $value, 'name' => $author, 'time' => time()];
        if ($append) {
            $currentValue = $this->__get($name);
            if (!is_array($currentValue)) {
                $currentValue = [];
            }
            array_unshift($currentValue, $value);
            $this->__set($name, $currentValue);
        } else {

            $this->__set($name, [$value]);
        }
    }
}