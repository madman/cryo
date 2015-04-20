<?php
namespace Storage\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BloodForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                'text',
                ['label' => "Шифр"]
            )
            ->add(
                'gender',
                'text',
                ['label' => "Стать"]
            )
            ->add(
                'is_check_mother_blood',
                'text',
                ['label' => "Перевірка материнської крові"]
            )
            ->add(
                'jadk',
                'text',
                ['label' => "Кількість ЯДК, 10*6/мл"]
            )
            ->add(
                'viability',
                'text',
                ['label' => "Життєздатність, %"]
            )
            ->add(
                'volume',
                'text',
                ['label' => "Об'єм"]
            )
            ->add(
                'count',
                'text',
                ['label' => "Кількість"]
            )

            ;
    }

    public function getName()
    {
        return 'blood_form';
    }
}
