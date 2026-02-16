<?php

namespace App\Form;

use App\Entity\Fiche;
use App\Entity\Filiere;
use App\Service\FicheIconService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FicheType extends AbstractType
{
    public function __construct(private FicheIconService $ficheIconService)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Entrez le titre de la fiche',
                    'class' => 'form-control',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le titre est obligatoire.'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Entrez le contenu de la fiche',
                    'class' => 'form-control',
                    'rows' => 10,
                    'maxlength' => 10000
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le contenu est obligatoire.'
                    ]),
                    new Assert\Length([
                        'min' => 20,
                        'max' => 10000,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères pour être utile.',
                        'maxMessage' => 'Le contenu ne peut pas dépasser {{ limit }} caractères.'
                    ])
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
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône de la fiche',
                'choices' => $this->ficheIconService->getIconChoices(),
                'placeholder' => 'Choisir une icône',
                'required' => false,
                'attr' => [
                    'class' => 'form-control fiche-icon-select',
                    'data-icon-path' => $this->ficheIconService->getIconPath()
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
