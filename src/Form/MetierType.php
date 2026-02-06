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

class MetierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du métier',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Développeur Web'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez ce métier...'
                ]
            ])
            ->add('salaireeMoyen', NumberType::class, [
                'label' => 'Salaire moyen (DT)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 2500',
                    'step' => '0.01'
                ]
            ])
            ->add('secteur', TextType::class, [
                'label' => 'Secteur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Informatique, Commerce, Design...'
                ]
            ])
            ->add('niveauEtude', ChoiceType::class, [
                'label' => 'Niveau d\'étude requis',
                'required' => false,
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
