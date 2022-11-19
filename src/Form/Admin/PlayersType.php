<?php

namespace App\Form\Admin;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\RulesetEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $player = $builder->getData();

        $builder
            ->add(
                'journalier',
                CheckboxType::class,
                [
                    'label' => 'Journalier ?',
                    'required' => false
                ]
            )
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('nr', IntegerType::class, ['label' => 'Numero'])
            ->add('dateBought', DateType::class,
                ['widget' => 'single_text', 'html5' => true, 'label' => 'Date Engagement'])
            ->add('dateSold', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'empty_data' => '',
                'label' => 'Date Renvoi'
            ])
            ->add('achMa', IntegerType::class, ['label' => 'Bonus Ma'])
            ->add('achSt', IntegerType::class, ['label' => 'Bonus Fo'])
            ->add('achAg', IntegerType::class, ['label' => 'Bonus Ag'])
            ->add('achAv', IntegerType::class, ['label' => 'Bonus Av']);

        if ($player && $player->getRuleset() == RulesetEnum::BB_2020) {
            $builder->add('achCp', IntegerType::class, ['label' => 'Bonus Cp']);
        }

        $builder
            ->add('sppDepense', IntegerType::class, ['label' => 'Spp dépensé'])
            ->add('value', IntegerType::class, ['label' => 'Valeur'])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => ['Ok' => 1, 'Vendu' => 7, 'Mort' => 8, 'Xp' => 9],
                    'label' => 'Status',
                ]
            )
            ->add('dateDied', DateType::class,
                ['widget' => 'single_text', 'html5' => true, 'required' => false, 'label' => 'Date Mort'])
            ->add('injMa', IntegerType::class, ['label' => 'Blessure Ma'])
            ->add('injSt', IntegerType::class, ['label' => 'Blessure Fo'])
            ->add('injAg', IntegerType::class, ['label' => 'Blessure Ag'])
            ->add('injAv', IntegerType::class, ['label' => 'Blessure Av'])
            ->add('injNi', IntegerType::class, ['label' => 'Niggling']);

        if ($player && $player->getRuleset() == RulesetEnum::BB_2020) {
            $builder->add('injCp', IntegerType::class, ['label' => 'Bonus Cp']);
        }

        $builder
            ->add('injRpm', IntegerType::class, ['label' => 'RPM'])
            ->add('photo');

        /** @var Players $player */
        if ($player && $player->getRuleset() == RulesetEnum::BB_2016) {
            $builder->add('fPos', EntityType::class, [
                'class' => GameDataPlayers::class,
                'choice_label' => 'pos',
                'group_by' => 'fRace.name',
                'label' => 'Race'
            ])->add('fRid', EntityType::class,
                ['class' => Races::class, 'choice_label' => 'name', 'label' => 'Position']);
        }

        if ($player && $player->getRuleset() == RulesetEnum::BB_2020) {
            $builder->add('fPosBb2020', EntityType::class, [
                'class' => GameDataPlayersBb2020::class,
                'choice_label' => 'pos',
                'group_by' => 'race.name',
                'label' => 'Race'
            ])->add('fRidBb2020', EntityType::class,
                ['class' => RacesBb2020::class, 'choice_label' => 'name', 'label' => 'Position']);
        }

        $builder
            ->add('ownedByTeam', EntityType::class,
                ['class' => Teams::class, 'choice_label' => 'name', 'label' => 'Equipe',
                    'group_by' => function (Teams $equipe) {
                            return AnneeEnum::numeroToAnnee()[$equipe->getYear()] . ' - ' . RulesetEnum::numeroVersEtiquette()[$equipe->getRuleset()];
                    }])
            ->add('icon', EntityType::class, [
                'class' => PlayersIcons::class,
                'choice_label' => 'icon_name',
                'group_by' => 'position.pos',
                'label' => 'Icone'
            ])
            ->add(
                'ruleset',
                ChoiceType::class,
                [
                    'choices' => ['BB2016' => 0, 'BB2020' => 1],
                    'label' => 'Ruleset',
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Players::class,
        ]);
    }
}
