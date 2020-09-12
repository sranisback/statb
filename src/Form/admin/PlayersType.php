<?php

namespace App\Form\admin;

use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\Teams;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('name')
            ->add('nr')
            ->add('dateBought')
            ->add('dateSold')
            ->add('achMa')
            ->add('achSt')
            ->add('achAg')
            ->add('achAv')
            ->add('extraSpp')
            ->add('extraVal')
            ->add('value')
            ->add('status')
            ->add('dateDied')
            ->add('injMa')
            ->add('injSt')
            ->add('injAg')
            ->add('injAv')
            ->add('injNi')
            ->add('injRpm')
            ->add('photo')
            ->add('fPos', EntityType::class, ['class' => GameDataPlayers::class,'choice_label' =>'pos'])
            ->add('fRid', EntityType::class, ['class' => Races::class,'choice_label' =>'name'])
            ->add('ownedByTeam', EntityType::class, ['class' => Teams::class,'choice_label' =>'name'])
            ->add('icon', EntityType::class, ['class' => PlayersIcons::class,'choice_label' =>'icon_name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Players::class,
        ]);
    }
}
