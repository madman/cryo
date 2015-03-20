<?php
namespace Users\Form;

use Core\Validator\Constraints\CurrentUserPassword;
use Core\Validator\Constraints\ChangeUserPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ProfileForm extends AbstractType
{
    public $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', ['label' => $this->app->trans('Id'), 'required' => false, 'disabled' => 'disabled'])
            ->add(
                'email',
                'text',
                ['label' => $this->app->trans('Email'), 'required' => false, 'disabled' => 'disabled']
            )
            ->add(
                'currency',
                'text',
                ['label' => $this->app->trans('Валюта'), 'required' => false, 'disabled' => 'disabled']
            )
            ->add(
                'current_password',
                'password',
                [
                    'label'       => $this->app->trans('Текущий пароль'),
                    'constraints' => [new CurrentUserPassword(['userPassword' => $options['data']->getPassword()])]
                ]
            )
            ->add(
                'new_password',
                'repeated',
                [
                    'type'            => 'password',
                    'first_options'   => array('label' => $this->app->trans('Изменить пароль')),
                    'second_options'  => array('label' => $this->app->trans('Еще раз пароль')),
                    'constraints'     => [
                        new NotBlank(),
                        new Length(['min' => 3, 'max' => 30]),
                        new ChangeUserPassword(['userPassword' => $options['data']->getPassword()])
                    ],
                    'invalid_message' => $this->app->trans('Значения паролей не совпадают'),
                ]
            );
    }

    public function getName()
    {
        return 'profile_form';
    }
}
