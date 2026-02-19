<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\Filiere;
use App\Repository\FiliereRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereType extends AbstractType
{
    private FiliereRepository $filiereRepository;

    public function __construct(FiliereRepository $filiereRepository)
    {
        $this->filiereRepository = $filiereRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $normalize = static function (?string $value): string {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }
            $value = mb_strtolower($value, 'UTF-8');
            return str_replace(
                ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç'],
                ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c'],
                $value
            );
        };

        $filieres = $this->filiereRepository->findActives();
        $uniqueFilieres = [];

        foreach ($filieres as $filiere) {
            $nom = $filiere->getNom();
            $key = $normalize($nom);
            if (!isset($uniqueFilieres[$key])) {
                $uniqueFilieres[$key] = $filiere;
            }
        }

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la matière',
            ])
            ->add('niveau', ChoiceType::class, [
                'label' => 'Niveau',
                'mapped' => false,
                'required' => false,
                'placeholder' => 'Sélectionner un niveau',
                'choices' => [
                    'Baccalauréat' => 'Bac',
                    'Autre niveau' => 'Autre',
                ],
            ])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choices' => array_values($uniqueFilieres),
                'choice_label' => static function (Filiere $f): string {
                    return (string) $f->getNom();
                },
                'label' => 'Filiere',
                'placeholder' => 'Sélectionner une filière',
                'choice_attr' => static function (Filiere $f): array {
                    return ['data-niveau' => $f->getNiveau()];
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
