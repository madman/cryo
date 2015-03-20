<?php

namespace Users\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Core\Validator\Constraints\DocumentExists;

class RemindForm extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $documentValidator = new DocumentExists(
            [
                'message'    => $this->app->trans('Пользователь не найден.'),
                'collection' => 'users',
                'property'   => 'email'
            ]
        );

        $builder->add(
            'email',
            'email',
            [
                'constraints' => [new Email, new NotBlank, $documentValidator]
            ]
        );
    }

    public function getName()
    {
        return 'remind_form';
    }
}
