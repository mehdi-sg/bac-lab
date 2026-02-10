<?php

namespace App\Form;

use App\Entity\Groupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du groupe de révision',
                'attr' => [
                    'class' => 'form-control', // Classe CSS de Swipe/Bootstrap
                    'placeholder' => 'Ex: Révision Math Techniques'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description / Objectifs',
                'attr' => [
                    'class' => 'form-control', // Classe CSS de Swipe/Bootstrap
                    'placeholder' => 'De quoi allez-vous discuter ?',
                    'rows' => 3
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
            // --- TRÈS IMPORTANT (CONSIGNE PIDEV) ---
            // Désactive la validation automatique du navigateur (HTML5)
            // Pour que seul le contrôle de saisie serveur (PHP) soit utilisé.
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}