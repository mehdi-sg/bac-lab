<?php

namespace App\Form;

use App\Entity\Fiche;
use App\Entity\Filiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Entrez le titre de la fiche',
                    'class' => 'form-control',
                    'maxlength' => 255
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Entrez le contenu de la fiche',
                    'class' => 'form-control',
                    'rows' => 10,
                    'maxlength' => 10000
                ]
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Fiche publique',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => function (Filiere $filiere) {
                    return $filiere->getNom() . ' (' . $filiere->getNiveau() . ')';
                },
                'label' => 'Filière',
                'placeholder' => 'Sélectionnez une filière',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fiche::class,
        ]);
    }
}
