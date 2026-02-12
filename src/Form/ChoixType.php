<?php

namespace App\Form;

use App\Entity\Choix;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChoixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'constraints' => [
                    new NotBlank(['message' => 'Le libellé est obligatoire.']),
                ],
                'attr' => [
                    'placeholder' => 'Entrez le texte du choix',
                ],
            ])
            ->add('estCorrect', CheckboxType::class, [
                'label' => 'Bonne réponse',
                'required' => false,
            ])
            ->add('question', EntityType::class, [
                'label' => 'Question',
                'class' => Question::class,
                'choice_label' => 'enonce',
                'constraints' => [
                    new NotBlank(['message' => 'La question est obligatoire.']),
                ],
                'placeholder' => '-- Sélectionner une question --',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Choix::class,
        ]);
    }
}
