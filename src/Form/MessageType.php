<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre du message est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre du message (ex: Cours : Nom du cours)'
                ]
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le contenu du message est obligatoire']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 5000,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le contenu ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Contenu du message'
                ]
            ])
            ->add('student', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de l\'étudiant ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de l\'étudiant'
                ]
            ])
            ->add('teacher', TextType::class, [
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
            ->add('isRead', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut de lecture est obligatoire'])
                ],
                'choices' => [
                    'Non lu' => 0,
                    'Lu' => 1
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('messageCount', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de messages est obligatoire']),
                    new Assert\Positive([
                        'message' => 'Le nombre de messages doit être un nombre positif'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 9999,
                        'message' => 'Le nombre de messages ne peut pas dépasser 9999'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nombre de messages dans la discussion'
                ]
            ])
            ->add('lastMessage', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 2000,
                        'maxMessage' => 'Le dernier message ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Dernier message de la discussion (optionnel)'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            // Désactivation de la validation HTML5
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
