<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', null, [
            'required' => true,
            'label' => 'Email',
            'attr' => [
                'placeholder' => 'email@email.fr',
                'class' => 'mt-2'
            ]
        ])
        ->add('pseudo', null,[
            'required' => true,
            'label' => 'Pseudo',
            'attr' => [ 
                'placeholder' => 'MartinD14',
                'class' => 'mt-2'
            ]
        ])
        ->add('password', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, [
            'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
            'invalid_message' => 'Les mots de passes ne correspondent pas',
            'required' => true,
            'first_options' => ['label' => 'Mot de passe', 'attr' => [ 'class' => 'mt-2', 'placeholder' => 'Votre mot de passe' ]],
            'second_options' => ['label' => 'Confirmation mot de passe', 'attr' => [ 'class' => 'mt-2', 'placeholder' => 'Confirmation de votre mot de passe' ]]
        ])
        ->add('firstName', null, [
            'required' => true,
            'label' => 'Prénom',
            'attr' => [
                'placeholder' => 'Martin',
                'class' => 'mt-2'
            ]
        ])
        ->add('lastName', null, [
            'required' => true,
            'label' => 'Nom',
            'attr' => [
                'placeholder' => 'Dupont',
                'class' => 'mt-2'
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
