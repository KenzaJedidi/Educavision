<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre du cours est obligatoire']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9\sàâäéèêëïîôöùûüçÀÂÄÉÈÊËÏÎÔÖÙÛÜÇ\-\,\.\:\!\?]+$/',
                        'message' => 'Le titre contient des caractères non valides'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Introduction à Symfony 6',
                    'maxlength' => 255
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 2000,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Décrivez en détail le contenu du cours, les prérequis, les objectifs...',
                    'maxlength' => 2000
                ]
            ])
            ->add('imageUrl', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le chemin de l\'image ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom du fichier image (ex: mon-cours.jpg)'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La catégorie est obligatoire'])
                ],
                'choices' => [
                    'Développement' => 'Développement',
                    'Design' => 'Design',
                    'Marketing' => 'Marketing',
                    'Business' => 'Business',
                    'Science' => 'Science',
                    'Langues' => 'Langues',
                    'Autre' => 'Autre'
                ],
                'placeholder' => 'Choisissez une catégorie',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'Le prix doit être un nombre positif ou nul'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 9999.99,
                        'message' => 'Le prix ne peut pas dépasser 9999.99 €'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'min' => '0'
                ]
            ])
            ->add('pdfUpload', FileType::class, [
                'label' => 'Fichier PDF du cours',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide.',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => '.pdf'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est obligatoire'])
                ],
                'choices' => [
                    'Actif' => 1,
                    'Inactif' => 0
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
            // Désactivation de la validation HTML5
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
