<?php

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Booking;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate',DateType::class, [
                'widget' => 'single_text',
                'label' => "Date d'arrivée",
                'attr' => ['placeholder' => "Selectionnez votre date d'arrivée"],
            ])
            ->add('endDate',DateType::class, [
                'widget' => 'single_text',
                'label' => "Date de départ",
                'attr' => ['placeholder' => "Selectionnez votre date de départ"],
            ])
            ->add('comment', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Une demande particulière à nous soumettre ...',
                    'rows' => 5
                ],
            ])
            ->add('guest', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'label' => "Le voyageur",
            ])
            ->add('advert', EntityType::class, [
                'class' => Advert::class,
                'choice_label' => 'title',
                'label' => "Annonce",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups' => [
                "Default",
            ]
        ]);
    }
}
