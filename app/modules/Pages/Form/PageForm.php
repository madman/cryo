<?php

namespace Pages\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\Validator\Constraints\UniqueTypeDocument;
use Core\Config;

abstract class PageForm extends AbstractType
{
    protected $app;
    protected $config;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'  => 'Pages\Entity\Page',
            'constraints' => [
                new UniqueTypeDocument(['collection' => 'pages', 'property' => 'slug', 'message' => $this->app->trans('Такой slug уже используется')])
            ]
        ]);
    }
}
