<?php

namespace App\Form\admin;

use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\Teams;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => ['Regulier' => 1, 'Journalier' => 2],
                    'label' => 'Type',
                ]
            )
            ->add('name')
            ->add('nr')
            ->add('dateBought', DateType::class, ['widget'=>'single_text', 'html5' => true])
            ->add('dateSold', DateType::class, ['widget'=>'single_text', 'html5' => true])
            ->add('achMa')
            ->add('achSt')
            ->add('achAg')
            ->add('achAv')
            ->add('extraSpp')
            ->add('extraVal')
            ->add('value')
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => ['Ok' => 1, 'Vendu' => 7, 'Mort' => 8, 'Xp' => 9],
                    'label' => 'Status',
                ]
            )
            ->add('dateDied', DateType::class, ['widget'=>'single_text', 'html5' => true])
            ->add('injMa')
            ->add('injSt')
            ->add('injAg')
            ->add('injAv')
            ->add('injNi')
            ->add('injRpm')
            ->add('photo')
            ->add('fPos', EntityType::class, [
                'class' => GameDataPlayers::class,
                'choice_label' =>'pos',
                'group_by' => 'fRace.name'
            ])
            ->add('fRid', EntityType::class, ['class' => Races::class,'choice_label' =>'name'])
            ->add('ownedByTeam', EntityType::class, ['class' => Teams::class, 'choice_label' =>'name'])
            ->add('icon', EntityType::class, [
                'class' => PlayersIcons::class,
                'choice_label' =>'icon_name',
                'group_by' => 'position.pos'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Players::class,
        ]);
    }
}
