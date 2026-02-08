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

class OffreStagEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateTransformer = new DateTimeTransformer('Y-m-d H:i:s');
        
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du stage',
                'attr' => [
                    'placeholder' => 'Ex: Développeur Web Junior',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez les missions et responsabilités...',
                    'rows' => 5,
                    'class' => 'form-control'
                ]
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'attr' => [
                    'placeholder' => 'Ex: Acme Corp',
                    'class' => 'form-control'
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Paris, France',
                    'class' => 'form-control'
                ]
            ]);

        $dateDebutField = $builder->create('dateDebut', TextType::class, [
            'label' => 'Date de début',
            'required' => false,
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
                'attr' => [
                    'min' => 1,
                    'class' => 'form-control'
                ]
            ])
            ->add('salaire', NumberType::class, [
                'label' => 'Salaire mensuel (DT)',
                'required' => false,
                'attr' => [
                    'step' => '0.01',
                    'min' => '0',
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 2500'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
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
