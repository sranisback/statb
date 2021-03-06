<?php


namespace App\Form;

use App\Entity\GameDataStadium;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreerStadeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add("nom", TextType::class, ['label'=>"Nom du stade", 'required' => 'true'])
        ->add(
            'fTypeStade',
            EntityType::class,
            [
                'class' => GameDataStadium::class,
                'choice_label' => 'type',
                'group_by' => 'famille',
                'label' => 'Choisir un type',
            ]
        )
        ->add('niveau', ChoiceType::class, [
            'choices' => [
                'Prairie Verte' => 0,
                'Terrain aménagé - 150 000 Po' => 1,
                'Terrain bien aménagé - 250 000 Po' => 2,
                'Stade Correct - 500 000 Po' => 3,
                'Stade Ultra moderne  - 750 000 Po' => 4,
                'Résidence' => 5
            ],
            'label' => 'Niveau du stades'
        ])
        ->add('submit', SubmitType::class, ['label' => 'Construire'])
        ->add('cancel', ButtonType::class, ['label'=>'Annuler','attr'=>['data-dismiss'=>'modal']])
        ->getForm();
    }
}
