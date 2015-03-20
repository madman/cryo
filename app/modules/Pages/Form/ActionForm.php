<?php

namespace Pages\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;
use Core\Validator\Constraints\CorrectSlug;
use Core\Form\Type\UploadedImageType;

class ActionForm extends PageForm
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pageConfig = $this->config->get('image/pages');
        $builder
            ->add('name', 'text', ['label' => $this->app->trans('Название'), 'constraints' => [new NotBlank()]])
            ->add('slug', 'text', ['label' => $this->app->trans('Slug'), 'constraints' => [new NotBlank(), new CorrectSlug()]]);

        if (isset($options['data']) && $options['data']->getImage() !== null) {
            $builder->add('image', new UploadedImageType(), ['label' => $this->app->trans('Изображения'), 'attr' => ['src' => CORE_UPLOADS_URL . '/actions/' . $options['data']->getImage()]]);
        }

        $builder
            ->add('file', 'file', [
                'label'       => $this->app->trans('Файл изображения'),
                'required'    => false,
                'constraints' => [new Image(['maxWidth' => $pageConfig['max_width'], 'maxHeight' => $pageConfig['max_height'], 'mimeTypes' => $pageConfig['allowed_type']])]])
            ->add('short_content', 'textarea', ['label' => $this->app->trans('Краткое содержание'), 'required' => false, 'constraints' => [new NotBlank()]])
            ->add('content', 'textarea', ['label' => $this->app->trans('Содержание'), 'required' => false, 'constraints' => [new NotBlank()]])
            ->add('title', 'text', ['constraints' => [new NotBlank()]])
            ->add('description', 'text', ['label' => $this->app->trans('Мета тег Description'), 'required' => false])
            ->add('keywords', 'text', ['label' => $this->app->trans('Мета тег Keywords'), 'required' => false])
            ->add('created_at', 'text', [
                'label' => $this->app->trans('Дата Создания'),
                'data'  => (isset($options['data']) && $options['data']->getCreatedAt() !== null) ?
                        (new \DateTime($options['data']->getCreatedAt()))->format('Y-m-d H:i') :
                        (new \DateTime())->format('Y-m-d H:i')
            ])
            ->add('is_active', 'checkbox', ['label' => $this->app->trans('Активная?'), 'required' => false]);
    }

    public function getName()
    {
        return 'action';
    }
}
