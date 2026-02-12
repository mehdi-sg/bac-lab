<?php

namespace App\Form;

use App\Entity\Chapitre;
use App\Entity\Matiere;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du Quiz',
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire.']),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Quiz de Mathématiques - Chapitre 1',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez brièvement le contenu du quiz...',
                    'rows' => 4,
                ],
            ])
            ->add('niveau', ChoiceType::class, [
                'label' => 'Niveau',
                'choices' => [
                    'Facile' => 'Facile',
                    'Moyen' => 'Moyen',
                    'Difficile' => 'Difficile',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le niveau est obligatoire.']),
                ],
                'placeholder' => '-- Sélectionner --',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire.']),
                    new Range([
                        'min' => 1,
                        'max' => 180,
                        'notInRangeMessage' => 'La durée doit être entre {{ min }} et {{ max }} minutes.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => '20',
                ],
            ])
            ->add('matiere', EntityType::class, [
                'label' => 'Matière',
                'class' => Matiere::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank(['message' => 'La matière est obligatoire.']),
                ],
                'placeholder' => '-- Sélectionner une matière --',
            ])
            ->add('chapitre', EntityType::class, [
                'label' => 'Chapitre',
                'class' => Chapitre::class,
                'choice_label' => 'titre',
                'constraints' => [
                    new NotBlank(['message' => 'Le chapitre est obligatoire.']),
                ],
                'placeholder' => '-- Sélectionner un chapitre --',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
