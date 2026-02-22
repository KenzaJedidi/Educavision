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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre du cours'
                ]
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre du cours'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Description détaillée du cours'
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
            ->add('price', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'constraints' => [
                    new Assert\Positive([
                        'message' => 'Le prix doit être un nombre positif'
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
            ->add('category', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La catégorie ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Catégorie du cours'
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
                'label' => 'Statut',
                'choices' => [
                    'Brouillon' => 'draft',
                    'Publié' => 'published'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

        // Add event listener to handle backward compatibility
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (isset($data['titre']) && !empty($data['titre'])) {
                $data['title'] = $data['titre'];
                $event->setData($data);
            }
        });
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
