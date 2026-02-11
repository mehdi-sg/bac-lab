<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
           ->add('plainPassword', PasswordType::class, [
    'label' => 'Mot de passe',
    'mapped' => false,
    'constraints' => [
        new Assert\NotBlank([
            'message' => 'Veuillez entrer un mot de passe',
        ]),
        new Assert\Length([
            'min' => 8,
            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
        ]),
        new Assert\Regex([
            'pattern' => '/^(?=.*[0-9])(?=.*[\W_]).+$/',
            'message' => 'Le mot de passe doit contenir au moins un chiffre et un caractère spécial',
        ]),
    ],
])

            ->add('profil', ProfilType::class, [
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

