<?php
namespace Storage\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

use Storage\Entity\Blood;

class BloodForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                'hidden'
            )
            ->add(
                'gender',
                'choice',
                [
                    'label' => "Стать",
                    'choices' => Blood::genderList(),
                    'empty_value' => '--Вкажіть стать--',
                    'required' => true,
                ]
            )
            ->add(
                'blood_group',
                'choice',
                [
                    'label' => "Група крові",
                    'choices' => Blood::groupLIst(),
                    'empty_value' => '--Вкажіть групу крові--',
                    'required' => true,
                ]
            )
            ->add(
                'rh',
                'choice',
                [
                    'label' => "Резус",
                    'choices' => Blood::rhList(),
                    'empty_value' => '--Вкажіть резус--',
                    'required' => true,
                ]
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
                'number',
                ['label' => "Життєздатність, %"]
            )
            ->add(
                'volume',
                'text',
                ['label' => "Об'єм"]
            )
            ->add(
                'blood_count',
                'number',
                ['label' => "Кількість"]
            );
    }

    public function getName()
    {
        return 'blood_form';
    }
}
