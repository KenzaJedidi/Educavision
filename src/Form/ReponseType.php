<?php

namespace App\Form;

use App\Entity\Reponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Réponse',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La réponse est obligatoire.']),
                    new Length([
                        'min' => 5,
                        'max' => 2000,
                        'minMessage' => 'La réponse doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La réponse ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 2000,
                    'rows' => 5,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
