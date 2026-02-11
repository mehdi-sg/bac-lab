<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Filiere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;
        
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du groupe de révision',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Révision Math Techniques',
                ],
                'required' => !$isEdit,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description / Objectifs',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'De quoi allez-vous discuter ?',
                    'rows' => 3,
                ],
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Groupe public',
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'label_attr' => [
                    'class' => 'form-check-label',
                ],
                'required' => false,
                'data' => true,
            ])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => 'nom',
                'label' => 'Filière',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'placeholder' => 'Sélectionner une filière (optionnel)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
            'is_edit' => false,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
