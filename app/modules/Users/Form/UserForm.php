<?php
namespace Users\Form;

use Core\Validator\Constraints\CurrentUserPassword;
use Core\Validator\Constraints\ChangeUserPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class UserForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                ['label' => "Ім'я користувача"]
            )
            ->add(
                'password',
                'text',
                ['label' => "Пароль"]
            );
    }

    public function getName()
    {
        return 'user_form';
    }
}
