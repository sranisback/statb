<?php

namespace App\Form\Admin;

use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\RulesetEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fans', IntegerType::class, ['label' => 'Fans'])
            ->add('ffactor1', IntegerType::class, ['label' => 'Mvt pop Equipe 1'])
            ->add('ffactor2', IntegerType::class, ['label' => 'Mvt pop Equipe 2'])
            ->add('income1', IntegerType::class, ['label' => 'Revenus Equipe 1'])
            ->add('income2', IntegerType::class, ['label' => 'Revenus Equipe 2'])
            ->add('dateCreated', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'empty_data' => '',
                'label' => 'Date de création'
            ])
            ->add('team1Score', IntegerType::class, ['label' => 'Score Equipe 1'])
            ->add('team2Score', IntegerType::class, ['label' => 'Score Equipe 2'])
            ->add('tv1', IntegerType::class, ['label' => 'Tv Equipe 1'])
            ->add('tv2', IntegerType::class, ['label' => 'Tv Equipe 2'])
            ->add(
                'stadeAcceuil',
                ChoiceType::class,
                [
                    'choices' => ['Equipe 1' => 1, 'Equipe 2 ' => 2, 'Personne' => 3],
                    'label' => 'Stade Acceuil'
                ]
            )
            ->add('depense1', IntegerType::class, ['label' => 'Dépenses Equipe 1'])
            ->add('depense2', IntegerType::class, ['label' => 'Dépenses Equipe 2'])
            ->add('team1', EntityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Equipe 1',
                    'group_by' => function (Teams $equipe) {
                        return RulesetEnum::numeroVersEtiquette()[$equipe->getRuleset()];
                    }
                ])
            ->add('team2', EntityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Equipe 2',
                    'group_by' => function (Teams $equipe) {
                        return AnneeEnum::numeroToAnnee()[$equipe->getYear()] . ' - ' . RulesetEnum::numeroVersEtiquette()[$equipe->getRuleset()];
                    }
                ])
            ->add('fMeteo', EntityType::class, ['class' => Meteo::class, 'choice_label' => 'nom', 'label' => 'Météo'])
            ->add('fStade', EntityType::class, [
                'class' => GameDataStadium::class,
                'choice_label' => 'type',
                'group_by' => 'famille',
                'label' => 'Type de stade'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matches::class,
        ]);
    }
}
