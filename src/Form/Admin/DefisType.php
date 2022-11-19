<?php

namespace App\Form\Admin;

use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\RulesetEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('defieRealise',
                CheckboxType::class,
                [
                    'label' => 'Défi réalisé ?',
                    'required' => false
                ])
            ->add('dateDefi', DateType::class,
                ['widget' => 'single_text', 'html5' => true, 'label' => 'Date du défi', 'required' => false])
            ->add('equipeDefiee', EntityType::class, [
                'class' => Teams::class,
                'choice_label' => 'name',
                'group_by' => function (Teams $equipe) {
                    return AnneeEnum::numeroToAnnee()[$equipe->getYear()] . ' - ' . RulesetEnum::numeroVersEtiquette()[$equipe->getRuleset()];
                }
            ])
            ->add(
                'matchDefi',
                EntityType::class,
                [
                    'class' => Matches::class,
                    'choice_label' => fn(Matches $match) => $match->getMatchId() . ' - ' .
                        ($match->getTeam1() ? $match->getTeam1()->getName() : '') . ' vs ' .
                        ($match->getTeam2() ? $match->getTeam2()->getName() : ''),
                    'required' => false
                ]
            )
            ->add('equipeOrigine', EntityType::class, [
                'class' => Teams::class,
                'choice_label' => 'name',
                'group_by' => function (Teams $equipe) {
                    return AnneeEnum::numeroToAnnee()[$equipe->getYear()] . ' - ' . RulesetEnum::numeroVersEtiquette()[$equipe->getRuleset()];
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Defis::class,
        ]);
    }
}
