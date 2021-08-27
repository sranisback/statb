<?php

namespace App\Form\Admin;

use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayersSkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'Normal' => 'N',
                        'Double' => 'D',
                        'Principale' => 'P',
                        'Principale hasard' => 'PH',
                        'Secondaire' => 'S',
                        'Secondaire hasard' => 'SH'
                    ],
                ]
            )
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
            ->add('fSkillBb2020', EntityType::class, [
                'class' => GameDataSkillsBb2020::class,
                'choice_label' =>'name',
                'group_by' =>  function (GameDataSkillsBb2020 $comp): string {
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
                'choice_label' =>fn (Players $joueur) => $joueur->getNr() . ', ' . $joueur->getName(),
                'group_by' =>  'ownedByTeam.name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => PlayersSkills::class,
        ]);
    }
}
