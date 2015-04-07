<?php

namespace Admin\Twig;

class TopMenuExtension extends \Twig_Extension
{

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getFunctions()
    {
        return [
            'render_modules_menu' => new \Twig_Function_Method($this, 'render_modules_menu'),
        ];
    }

    public function render_modules_menu()
    {
        foreach ($this->app['admin.menu.manager']->getItems() as $title => $items) {
            echo '<li class="dropdown">';
            echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">$title <b class=\"caret\"></b></a>";

            echo '<ul class="dropdown-menu">';

            ksort($items);
            foreach ($items as $title => $url) {
                echo '<li><a href="' . $url . '">' . $title . '</a></li>';
            }
            echo '</ul>';

            echo '</li>';
        }
    }

    public function getName()
    {
        return __CLASS__;
    }

}
