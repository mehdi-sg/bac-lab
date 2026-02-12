<?php

namespace App\Form;

use App\Entity\Chapitre;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChapitreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('contenu', TextareaType::class, ['label' => 'Contenu'])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'nom',
                'label' => 'Matière',
                'placeholder' => 'Choisir une matière',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Chapitre::class,
        ]);
    }
}
