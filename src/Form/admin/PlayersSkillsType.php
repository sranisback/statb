<?php

namespace App\Form\admin;

use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayersSkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type',
                ChoiceType::class,
                [
                    'choices' => ['Normal' => 'N', 'Double' => 'D'],
                ])
            ->add('fSkill', EntityType::class, [
                'class' => GameDataSkills::class,
                'choice_label' =>'name',
                'group_by' =>  function (GameDataSkills $comp): string {
                        $listeCategoriesCompetences =
                            [
                                '' => '',
                                'G' => 'Générale',
                                'A' => 'Agilité',
                                'P' => 'Passe',
                                'S' => 'Force',
                                'M' => 'Mutations',
                                'E' => 'Exceptionnelles',
                                'C' => 'Statistiques',
                            ];

                        return $listeCategoriesCompetences[$comp->getCat()];
                    }
            ])
            ->add('fPid', EntityType::class, [
                'class' => Players::class,
                'choice_label' =>'name',
                'group_by' =>  'ownedByTeam.name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlayersSkills::class,
        ]);
    }
}
