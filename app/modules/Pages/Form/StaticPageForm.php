<?php

namespace Pages\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Core\Validator\Constraints\CorrectSlug;
use Symfony\Component\Validator\Constraints\NotBlank;

class StaticPageForm extends PageForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', ['label' => $this->app->trans('Название'), 'constraints' => [new NotBlank()]])
            ->add('slug', 'text', ['label' => $this->app->trans('Slug'), 'constraints' => [new NotBlank(), new CorrectSlug()]])
            ->add('content', 'textarea', ['label' => $this->app->trans('Содержание'), 'constraints' => [new NotBlank()]])
            ->add('title', 'text', ['constraints' => [new NotBlank()]])
            ->add('description', 'text', ['label' => $this->app->trans('Мета тег Description'), 'required' => false])
            ->add('keywords', 'text', ['label' => $this->app->trans('Мета тег Keywords'), 'required' => false])
            ->add('is_active', 'checkbox', ['label' => $this->app->trans('Активная?'), 'required' => false]);
    }

    public function getName()
    {
        return 'static_page';
    }
}
