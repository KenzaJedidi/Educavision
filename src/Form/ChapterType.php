<?php

namespace App\Form;

use App\Entity\Chapter;
use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre du chapitre est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre du chapitre'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 5000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Description détaillée du chapitre'
                ]
            ])
            ->add('ordre', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Positive([
                        'message' => 'L\'ordre doit être un nombre positif'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 999,
                        'message' => 'L\'ordre ne peut pas dépasser 999'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ordre d\'affichage (optionnel)'
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
                    'placeholder' => 'Nom du fichier image (ex: mon-chapitre.jpg)'
                ]
            ])
            ->add('teacherName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom du professeur ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom du professeur'
                ]
            ])
            ->add('teacherEmail', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Email([
                        'message' => 'L\'adresse email n\'est pas valide'
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email du professeur'
                ]
            ])
            ->add('course', EntityType::class, [
                'required' => false,
                'class' => Course::class,
                'choice_label' => 'titre',
                'placeholder' => 'Sélectionnez un cours',
                'choices' => $options['teacher_courses'] ?? null,
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

        // If teacher_courses is not set, remove the 'choices' constraint to show all
        if ($options['teacher_courses'] === null) {
            $builder->add('course', EntityType::class, [
                'required' => false,
                'class' => Course::class,
                'choice_label' => 'titre',
                'placeholder' => 'Sélectionnez un cours',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
            'teacher_courses' => null,
            // Désactivation de la validation HTML5
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
