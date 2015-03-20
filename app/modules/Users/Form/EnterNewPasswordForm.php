<?php

namespace Users\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\Validator\Constraints\DocumentExists;

class EnterNewPasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'password',
            'password',
            [
                'constraints' => new NotBlank
            ]
        );
    }

    public function getName()
    {
        return 'enter_password_form';
    }
}
