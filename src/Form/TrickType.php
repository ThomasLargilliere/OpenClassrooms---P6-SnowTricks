<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre du trick',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'mt-2'
                ]
            ])
            ->add('description', null, [
                'label' => 'Description du trick',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'mt-2',
                    'rows' => 10
                ]
            ])
            ->add('featuredPicture', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'required' => true,
                'label' => 'Image à la une (PNG / JPG | Taille max : 5mo)',
                'attr' => [
                    'class' => 'form-control mt-2'
                ],
                'data_class' => null,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '5m',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir une image sous format JPG ou PNG',
                    ])
                ],
            ])
            ->add('id_category', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'label' => 'Catégorie du trick',
                'attr' => [
                    'class' => 'mt-2'
                ],
                'class' => \App\Entity\Category::class,
                'choice_label' => 'label',
            ])
            ->add('image', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'required' => false,
                'label' => 'Images (PNG / JPG | Taille max : 20mo)',
                'attr' => [
                    'class' => 'form-control mt-2'
                ],
                'data_class' => null,
                'multiple' => true,
                'mapped' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\All([
                        'constraints' => [
                            new \Symfony\Component\Validator\Constraints\File([
                                'maxSize' => '20m',
                                'mimeTypes' => [
                                    'image/jpg',
                                    'image/jpeg',
                                    'image/png'
                                ],
                                'mimeTypesMessage' => 'Veuillez choisir une image sous format JPG ou PNG',
                            ])
                        ]
                    ])
                ],
            ])
            ->add('video', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => 'Lien vers des vidéos',
                'entry_type' => \Symfony\Component\Form\Extension\Core\Type\TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
                'prototype' => true,
                'entry_options' => [
                    'attr' => [
                        'placeholder' => 'Lien d\'une vidéo...',
                    ]
                ]
            ])
            ->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
                'label' => 'Ajouter le trick',
                'attr' => [
                    'class' => 'btn btn-success mt-2 mb-4',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
