<?php
namespace Pages\Entity;

use Core\Db\Entity;

abstract class Page extends Entity
{
    /**
     * @Field
     */
    protected $type;
    /**
     * @Field
     */
    protected $name;

    /**
     * @Field
     */
    protected $slug;

    /**
     * @Field
     */
    protected $title;

    /**
     * @Field
     */
    protected $content;

    /**
     * @Field
     */
    protected $description;

    /**
     * @Field
     */
    protected $keywords;

    /**
     * @Field
     */
    protected $is_active;

    /**
     * @Field
     */
    protected $created_at;

    /**
     * @Field
     */
    protected $updated_at;

    public function beforeSave()
    {
        $time   = ($this->created_at) ? : '';
        $format = 'Y-m-d H:i:s';

        if ($this->isNew()) {
            $this->created_at = (new \DateTime($time))->format($format);
        } else {
            if ($time && is_string($time)) {
                $this->created_at = (new \DateTime($time))->format($format);
            }
            $this->updated_at = (new \DateTime())->format($format);
        }
    }
}
