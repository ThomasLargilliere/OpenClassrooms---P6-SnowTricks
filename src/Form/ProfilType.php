<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('avatar', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
            'required' => false,
            'label' => false,
            'attr' => [ 
                'class' => 'mt-2'
            ],
            'data_class' => null,
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\File([
                    'maxSize' => '5120k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger des images sous format JPG / PNG',
                ])
            ],
        ])
        ->add('pseudo', null, [
            'required' => true,
            'label' => false,
            'attr' => [ 
                'placeholder' => 'Pseudo',
                'class' => 'fadeIn second'
            ]
        ])
        ->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class, [
            'required' => true,
            'label' => false,
            'attr' => [ 
                'placeholder' => 'email@email.fr',
                'class' => 'fadeIn third',
            ]
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
        ->add('plainPassword', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, [
            'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
            'invalid_message' => 'Les mots de passes ne correspondent pas.',
            'required' => false,
            'label' => false,
            'mapped' => false,
            'options' => ['attr' => ['class' => 'fadeIn third']],
            'first_options'  => ['label' => false, 'attr' => ['placeholder' => 'Nouveau mot de passe (laisser vide pour ne pas changer)']],
            'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Confirmation nouveau mot de passe']],
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
