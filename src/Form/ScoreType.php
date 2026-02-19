<?php

namespace App\Form;

use App\Service\ScoreCalculatorService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ScoreType extends AbstractType
{
    public function __construct(
        private ScoreCalculatorService $scoreCalculator
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $filiere = $options['filiere'];
        
        if (!$filiere || !$this->scoreCalculator->isValidFiliere($filiere)) {
            throw new \InvalidArgumentException('Filière invalide ou manquante');
        }
        
        // Récupérer les matières pour cette filière
        $matieres = $this->scoreCalculator->getMatieresByFiliere($filiere);
        
        // Ajouter les champs pour chaque matière
        foreach ($matieres as $code => $label) {
            $builder->add($code, NumberType::class, [
                'label' => $label,
                'required' => true,
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 20,
                    'step' => 0.01,
                    'class' => 'form-control score-input',
                    'placeholder' => 'Note sur 20'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir la note pour {{ label }}'
                    ]),
                    new Assert\Range([
                        'min' => 0,
                        'max' => 20,
                        'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }}'
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'La note doit être un nombre'
                    ])
                ],
                'help' => $code === 'MG' ? 'Votre moyenne générale du baccalauréat' : null
            ]);
        }
        
        $builder->add('calculer', SubmitType::class, [
            'label' => 'Calculer mon score FG',
            'attr' => [
                'class' => 'btn btn-primary btn-lg score-submit-btn'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filiere' => null,
            'attr' => [
                'class' => 'score-form',
                'novalidate' => false
            ]
        ]);
        
        $resolver->setRequired('filiere');
        $resolver->setAllowedTypes('filiere', 'string');
    }
}