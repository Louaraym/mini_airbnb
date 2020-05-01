<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'attr' => ['placeholder' => 'Votre email'],
                'constraints' => [
                    new Email([
                        'message' => "Votre adresse email n'est pas valide",
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de Passe',
                'attr' => ['placeholder' => 'Votre mot de Passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre mot de passe',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caratères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmation du Mot de Passe',
                'attr' => ['placeholder' => 'Confirmer votre mot de Passe'],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre Prénom'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit avoir au moins {{ limit }} caratères',
                        'max' => 50,
                        'maxMessage' => 'Votre prénom doit avoir au maximum {{ limit }} caratères',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre Nom de famille'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit avoir au moins {{ limit }} caratères',
                        'max' => 50,
                        'maxMessage' => 'Votre nom doit avoir au maximum {{ limit }} caratères',
                    ]),
                ],
            ])
            ->add('avatarUrl', UrlType::class, [
                'label' => 'Avatar',
                'attr' => ['placeholder' => 'Url de votre avatar'],
                'constraints' => [
                    new Url([
                        'message' => 'Url non valide',
                    ]),
                ],
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'Présentation',
                'attr' => ['placeholder' => 'Votre présentation globale'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre présentation',
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Votre présentation doit avoir au moins {{ limit }} caratères',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Présentation détaillée',
                'attr' => ['placeholder' => 'Votre présentation détaillée'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre présentation détaillée',
                    ]),
                    new Length([
                        'min' => 50,
                        'minMessage' => 'Votre présentation détaillée doit avoir au moins {{ limit }} caratères',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
