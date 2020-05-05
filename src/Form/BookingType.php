<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\FrenchDateToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchDateToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', TextType::class, [
                'label' => "Date d'arrivée",
                'attr' => ['placeholder' => "Selectionnez votre date d'arrivée"],
            ])
            ->add('endDate', TextType::class, [
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
        ;

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
