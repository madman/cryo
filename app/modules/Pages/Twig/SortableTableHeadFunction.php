<?php
namespace Pages\Twig;

/**
 * This function allow to generate (decorate) sortable table header
 * Class SortableTableHeadFunction
 * @package Pages\Twig
 */
class SortableTableHeadFunction extends \Twig_SimpleFunction
{

    public function  __construct($options = [])
    {
        parent::__construct($this->getName(), $this->getCallable(), $options);
    }

    public function getName()
    {
        return 'sortable_table_head';
    }

    public function getCallable()
    {
        return function ($baseUrl, $sortBy, $sortOrder, $sortName, $title) {

            $url = $baseUrl . '&sort=' . $sortName . '&order=';
            $url .= (($sortBy === $sortName) && $sortOrder === 'asc') ? 'desc' : 'asc';

            $str = '<a href="' . $url . '">' . $title;
            if ($sortBy === $sortName) {
                if ($sortOrder === 'asc') {
                    $str .= '<span class="caret caret-reversed"></span>';
                } else {
                    $str .= '<span class="caret"></span>';
                }
            }
            $str .= '</a>';

            echo $str;
        };
    }
}