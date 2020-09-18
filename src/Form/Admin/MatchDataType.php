<?php

namespace App\Form\admin;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('mvp')
            ->add('cp')
            ->add('td')
            ->add('intcpt')
            ->add('bh')
            ->add('si')
            ->add('ki')
            ->add('inj')
            ->add('agg')
            ->add(
                'fPlayer',
                EntityType::class,
                [
                'class' => Players::class,
                'choice_label' =>fn (Players $joueur) => $joueur->getNr() . ', ' . $joueur->getName() . ', ' . $joueur->getFPos()->getPos() .
                        ($joueur->getType() == 2 ? 'Journalier' : '' ),
                'group_by' =>  'ownedByTeam.name'
                ]
            )
            ->add(
                'fMatch',
                EntityType::class,
                [
                    'class' => Matches::class,
                    'choice_label' => fn (Matches $match) => $match->getMatchId() . ' - ' .
                            $match->getTeam1()->getName() . ' vs ' .
                            $match->getTeam2()->getName()
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => MatchData::class,
        ]);
    }
}
