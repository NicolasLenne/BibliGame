<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Console;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GameType extends AbstractType
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
            ->add('consoles', EntityType::class, [
                'label' => false,
                'class' => Console::class,
                'choice_label' => 'name',
                'multiple' => false,
                'placeholder' => 'Selectionnez la console',
                'expanded' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
