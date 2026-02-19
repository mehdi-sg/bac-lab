<?php

namespace App\Form;

use App\Entity\Ressource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RessourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur',
                'required' => false,
            ])
            ->add('typeFichier', ChoiceType::class, [
                'label' => 'Type de fichier',
                'choices' => [
                    'PDF' => 'PDF',
                    'VIDEO' => 'VIDEO',
                    'LIEN' => 'LIEN',
                ],
                'placeholder' => 'Choisir...',
            ])
            ->add('imageCouverture', TextType::class, [
                'label' => 'Image de couverture (URL)',
                'required' => false,
            ])
            ->add('tags', TextType::class, [
                'label' => 'Tags (séparés par virgules)',
                'required' => false,
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Cours' => 'Cours',
                    'Exercices' => 'Exercices',
                    'Corrigé' => 'Corrigé',
                    'Résumé' => 'Résumé',
                    'Bac blanc' => 'Bac blanc',
                ],
                'placeholder' => 'Choisir une catégorie...',
                'required' => false,
            ])
            ->add('estActive', null, [
                'label' => 'Active ?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}
