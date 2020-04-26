<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                    'label' => 'Titre',
                    'attr' => ['placeholder' => 'Titre de l\'anonce'],
                ])
            ->add('coverImage', UrlType::class, [
                'label' => 'Url',
                'attr' => ['placeholder' => 'Url de l\'image principale'],
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Description globale de l\'anonce'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description détaillée de l\'anonce',
                    'rows' => 7
                    ],
            ])
            ->add('rooms', IntegerType::class, [
                'label' => 'Chambres',
                'attr' => ['placeholder' => 'Le nombre de chambres disponibles'],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'attr' => ['placeholder' => 'Le prix par nuit'],
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
