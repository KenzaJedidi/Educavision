<?php

namespace App\Form;

use App\Entity\Candidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}\'\- ]+$/u',
                        'message' => 'Le nom ne doit contenir que des lettres, espaces, apostrophes ou tirets.'
                    ])
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}\'\- ]+$/u',
                        'message' => 'Le prénom ne doit contenir que des lettres, espaces, apostrophes ou tirets.'
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire.']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide.']),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire.']),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s\-\(\)]{8,20}$/',
                        'message' => 'Téléphone invalide: utilisez uniquement chiffres, espaces, +, -, () (8 à 20 caractères).'
                    ])
                ],
            ])
            ->add('niveauEtude', TextType::class, [
                'label' => "Niveau d'étude",
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => "Le niveau d'étude ne doit pas dépasser {{ limit }} caractères."
                    ])
                ],
            ])
            ->add('cv', FileType::class, [
                'label' => 'CV (PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotNull(['message' => 'Veuillez joindre votre CV (PDF).']),
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Veuillez téléverser un fichier PDF',
                    ])
                ],
            ])
            ->add('lettreMotivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La lettre de motivation est obligatoire.']),
                    new Length([
                        'min' => 20,
                        'max' => 5000,
                        'minMessage' => 'La lettre de motivation doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La lettre de motivation ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Expliquez vos motivations et disponibilités...',
                    'minlength' => 20,
                    'maxlength' => 5000,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}
