<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de la formation est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}0-9\s\'\-\.\,\&\+]+$/u',
                        'message' => 'Le nom contient des caractères non autorisés.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de la formation',
                    'minlength' => 2,
                    'maxlength' => 255,
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
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
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Description détaillée',
                    'minlength' => 10,
                    'maxlength' => 5000,
                ]
            ])
            ->add('duree', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'La durée doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La durée ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 100 heures, 3 mois',
                    'maxlength' => 100,
                ]
            ])
            ->add('niveau', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    '🎯 Débutant' => 'debutant',
                    '📖 Intermédiaire' => 'intermediaire',
                    '⚡ Avancé' => 'avance',
                    '👑 Expert' => 'expert'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le niveau est obligatoire.']),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prerequisTexte', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Les prérequis ne doivent pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Prérequis nécessaires',
                    'maxlength' => 5000,
                ]
            ])
            ->add('competencesAcquises', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Les compétences ne doivent pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Compétences que vous allez acquérir',
                    'maxlength' => 5000,
                ]
            ])
            ->add('image', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'URL de l\'image ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'URL de l\'image (optionnel)',
                    'maxlength' => 255,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}