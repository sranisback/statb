<?php

namespace App\Form\Admin;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Sponsors;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $team = $builder->getData();

        $builder
            ->add('name')
            ->add('treasury')
            ->add('apothecary')
            ->add('rerolls')
            ->add('ffBought')
            ->add('assCoaches')
            ->add('cheerleaders')
            ->add('retired')
            ->add('ff')
            ->add('elo')
            ->add('tv')
            ->add('year')
            ->add('logo')
            ->add('franchise')
            ->add('sponsor', EntityType::class, [
                'class' => Sponsors::class,
                'choice_label' =>'name',
                'placeholder' => 'Pas de sponsor',
                'required' => false
            ])
            ->add('ownedByCoach', EntityType::class, ['class' => Coaches::class,'choice_label' =>'Username']);

            if ($team && $team->getRuleset() == RulesetEnum::BB_2016) {
                $builder->add('fRace', EntityType::class, [
                    'class' => Races::class,
                    'choice_label' =>'name',
                    'placeholder' => 'Choisir une Race BB2016',
                    'required' => false
                ]);
            }

        if ($team && $team->getRuleset() == RulesetEnum::BB_2020) {
            $builder->add('race', EntityType::class, [
                'class' => RacesBb2020::class,
                'choice_label' =>'name',
                'placeholder' => 'Choisir une Race BB2020',
                'required' => false
            ]);
        }

        $builder->add('fStades', EntityType::class, ['class' => Stades::class,'choice_label' =>'nom'])
            ->add(
                'ruleset',
                ChoiceType::class,
                [
                    'choices' => ['BB2016' => 0, 'BB2020' => 1],
                    'label' => 'Ruleset',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Teams::class,
        ]);
    }
}
