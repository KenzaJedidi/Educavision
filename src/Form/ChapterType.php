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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre du chapitre'
                ]
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Titre du chapitre'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Contenu détaillé du chapitre',
                    'id' => 'chapter_content'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Contenu',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Contenu détaillé du chapitre'
                ]
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Position d\'affichage'
                ]
            ])
            ->add('ordre', IntegerType::class, [
                'label' => 'Position',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Position d\'affichage'
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
                'choice_label' => 'title',
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
                'choice_label' => 'title',
                'placeholder' => 'Sélectionnez un cours',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
        }

        // Add event listener to handle backward compatibility
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            
            // Map titre to title
            if (isset($data['titre']) && !empty($data['titre'])) {
                $data['title'] = $data['titre'];
            }
            
            // Map description to content
            if (isset($data['description']) && !empty($data['description'])) {
                $data['content'] = $data['description'];
            }
            
            // Map ordre to position
            if (isset($data['ordre']) && !empty($data['ordre'])) {
                $data['position'] = $data['ordre'];
            }
            
            $event->setData($data);
        });
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
