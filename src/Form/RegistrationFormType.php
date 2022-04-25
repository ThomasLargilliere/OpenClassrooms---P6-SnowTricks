<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', null, [
            'required' => true,
            'label' => false,
            'attr' => [
                'placeholder' => 'Email@email.fr',
                'class' => 'fadeIn first'
            ]
        ])
        ->add('pseudo', null,[
            'required' => true,
            'label' => false,
            'attr' => [ 
                'placeholder' => 'Pseudo',
                'class' => 'fadeIn second'
            ]
        ])
        ->add('plainPassword', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, [
            'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
            'invalid_message' => 'Les mots de passes ne correspondent pas.',
            'required' => true,
            'label' => false,
            'mapped' => false,
            'options' => ['attr' => ['class' => 'fadeIn third']],
            'first_options'  => ['label' => false, 'attr' => ['placeholder' => 'Mot de passe']],
            'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Confirmation mot de passe']],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit être de {{ limit }} caractères minimum',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('firstName', null, [
            'required' => true,
            'label' => false,
            'attr' => [
                'placeholder' => 'Prénom',
                'class' => 'fadeIn fourth'
            ]
        ])
        ->add('lastName', null, [
            'required' => true,
            'label' => false,
            'attr' => [
                'placeholder' => 'Nom',
                'class' => 'fadeIn five'
            ]
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
