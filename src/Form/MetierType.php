<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Entity\Metier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class MetierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du métier',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du métier est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}0-9\s\'\-\.\,\&\+\/]+$/u',
                        'message' => 'Le nom contient des caractères non autorisés.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Développeur Web',
                    'minlength' => 2,
                    'maxlength' => 255,
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire.']),
                    new Length([
                        'min' => 10,
                        'max' => 2000,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez ce métier...',
                    'minlength' => 10,
                    'maxlength' => 2000,
                ]
            ])
            ->add('salaireeMoyen', NumberType::class, [
                'label' => 'Salaire moyen (DT)',
                'required' => false,
                'constraints' => [
                    new PositiveOrZero(['message' => 'Le salaire doit être un nombre positif ou zéro.']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 2500',
                    'step' => '0.01',
                    'min' => '0',
                ]
            ])
            ->add('secteur', TextType::class, [
                'label' => 'Secteur',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le secteur est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le secteur doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le secteur ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Informatique, Commerce, Design...',
                    'maxlength' => 255,
                ]
            ])
            ->add('niveauEtude', ChoiceType::class, [
                'label' => 'Niveau d\'étude requis',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le niveau d\'étude est obligatoire.']),
                ],
                'choices' => [
                    'Bac' => 'Bac',
                    'Bac+2' => 'Bac+2',
                    'Bac+3' => 'Bac+3',
                    'Bac+5' => 'Bac+5',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('perspectivesEmploi', ChoiceType::class, [
                'label' => 'Perspectives d\'emploi',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Les perspectives d\'emploi sont obligatoires.']),
                ],
                'choices' => [
                    'Bon' => 'Bon',
                    'Moyen' => 'Moyen',
                    'Faible' => 'Faible',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('filiere', EntityType::class, [
                'label' => 'Filière',
                'class' => Filiere::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank(['message' => 'La filière est obligatoire.']),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Metier::class,
        ]);
    }
}
