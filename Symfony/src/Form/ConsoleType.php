<?php

namespace App\Form;

use App\Entity\Console;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' =>  ['class' => 'input'],
            ])
            ->add('isLoose', CheckboxType::class, [
                'label' => false,
            ])
            ->add('photo', TextType::class, [
                'label' => false,
                'attr' =>  ['class' => 'input'],
            ])
            ->add('comment', TextareaType::class, [
                'label' => false,
                'attr' =>  ['class' => 'textarea'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Console::class,
        ]);
    }
}
