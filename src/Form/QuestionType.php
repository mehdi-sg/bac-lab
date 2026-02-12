<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enonce', TextareaType::class, [
                'label' => "Énoncé de la question",
                'constraints' => [
                    new NotBlank(['message' => "L'énoncé est obligatoire."]),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Quelle est la dérivée de ln(x) ?',
                    'rows' => 4,
                ],
            ])
            ->add('typeQuestion', ChoiceType::class, [
                'label' => 'Type de question',
                'choices' => [
                    'QCM (Choix multiples)' => 'QCM',
                    'Vrai / Faux' => 'VRAI_FAUX',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type de question est obligatoire.']),
                ],
                'placeholder' => '-- Sélectionner --',
            ])
            ->add('score', IntegerType::class, [
                'label' => 'Points',
                'constraints' => [
                    new NotBlank(['message' => 'Le score est obligatoire.']),
                    new Range([
                        'min' => 1,
                        'max' => 20,
                        'notInRangeMessage' => 'Le score doit être entre {{ min }} et {{ max }}.',
                    ]),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 20,
                    'placeholder' => '1',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
