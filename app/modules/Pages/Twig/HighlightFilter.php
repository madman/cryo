<?php

namespace Pages\Twig;

/**
 * This filter is highlight given substring in the sting.
 * Class HighlightFilter
 * @package Pages\Twig
 */
class HighlightFilter extends \Twig_SimpleFilter
{
    public function __construct($options = [])
    {
        parent::__construct($this->getName(), $this->getCallable(), $options);
    }

    public function getName()
    {
        return 'highlight';
    }

    public function getCallable()
    {
        return function ($string, $search) {
            if (is_null($search) || empty($search)) {
                return $string;
            }

            return preg_replace("~({$search})~i", '<span class="search-highlight">${1}</span>', $string);
        };
    }
}