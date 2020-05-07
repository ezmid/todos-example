<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class TodoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttribute('name', '')
            ->add('name', Type\TextType::class)
            ->add('description', Type\TextareaType::class)
        ;
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
