<?php
namespace Pages\Entity;

use Pages\Entity\Page;

class StaticPage extends Page
{
    /**
     * @Field
     */
    protected $type = 'static-page';
}
