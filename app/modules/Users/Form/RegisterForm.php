<?php

namespace Users\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterForm extends AbstractType
{
    /**
     * @var $currencies Core\Currency
     */
    protected $currencies;

    public function __construct(\Core\Currency $currencies)
    {
        $this->currencies = $currencies;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('password', 'password')
            ->add(
                'currency',
                'choice',
                [
                    'choices'  => $this->getCurrencies(),
                    'expanded' => true,
                    'data'     => $this->currencies->getDefault()
                ]
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Users\Entity\User'
            ]
        );
    }

    public function getName()
    {
        return 'register_form';
    }

    protected function getCurrencies()
    {
        return array_combine($this->currencies->getAll(), $this->currencies->getAll());
    }
}
