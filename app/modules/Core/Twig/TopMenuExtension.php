<?php

namespace Core\Twig;

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
            'render_top_menu' => new \Twig_Function_Method($this, 'render_top_menu'),
        ];
    }

    public function render_top_menu()
    {
        echo '<ul>';
        foreach ($this->app['top.menu.items'] as $title => $url) {
            echo '<li><a href="' . $url . '">' . $title . '</a></li>';
        }
        echo '</ul>';
    }

    public function getName()
    {
        return __CLASS__;
    }

}
