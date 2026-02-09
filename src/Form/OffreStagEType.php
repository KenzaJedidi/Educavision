<?php

namespace App\Form;

use App\Entity\OffreStage;
use App\Form\DataTransformer\DateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Regex;

class OffreStagEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateTransformer = new DateTimeTransformer('Y-m-d H:i:s');
        
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du stage',
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire.']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le titre ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Développeur Web Junior',
                    'class' => 'form-control',
                    'minlength' => 3,
                    'maxlength' => 255,
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire.']),
                    new Length([
                        'min' => 10,
                        'max' => 5000,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Décrivez les missions et responsabilités...',
                    'rows' => 5,
                    'class' => 'form-control',
                    'minlength' => 10,
                    'maxlength' => 5000,
                ]
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de l\'entreprise est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom de l\'entreprise doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom de l\'entreprise ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Acme Corp',
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 255,
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le lieu doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le lieu ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Paris, France',
                    'class' => 'form-control',
                    'maxlength' => 255,
                ]
            ]);

        $dateDebutField = $builder->create('dateDebut', TextType::class, [
            'label' => 'Date de début',
            'required' => false,
            'constraints' => [
                new NotBlank(['message' => 'La date de début est obligatoire.']),
            ],
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'YYYY-MM-DD HH:MM:SS'
            ]
        ]);
        $dateDebutField->addModelTransformer($dateTransformer);
        $builder->add($dateDebutField);

        $dateFinField = $builder->create('dateFin', TextType::class, [
            'label' => 'Date de fin',
            'required' => false,
            'constraints' => [
                new NotBlank(['message' => 'La date de fin est obligatoire.']),
            ],
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'YYYY-MM-DD HH:MM:SS'
            ]
        ]);
        $dateFinField->addModelTransformer($dateTransformer);
        $builder->add($dateFinField);

        $builder
            ->add('dureeJours', IntegerType::class, [
                'label' => 'Durée en jours',
                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire.']),
                    new Positive(['message' => 'La durée doit être un nombre positif.']),
                ],
                'attr' => [
                    'min' => 1,
                    'class' => 'form-control'
                ]
            ])
            ->add('salaire', NumberType::class, [
                'label' => 'Salaire mensuel (DT)',
                'required' => false,
                'constraints' => [
                    new PositiveOrZero(['message' => 'Le salaire doit être positif ou zéro.']),
                ],
                'attr' => [
                    'step' => '0.01',
                    'min' => '0',
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 2500'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'constraints' => [
                    new NotBlank(['message' => 'Le statut est obligatoire.']),
                ],
                'choices' => [
                    'Ouvert' => 'Ouvert',
                    'Fermé' => 'Fermé',
                    'Pourvu' => 'Pourvu'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreStage::class,
        ]);
    }
}
