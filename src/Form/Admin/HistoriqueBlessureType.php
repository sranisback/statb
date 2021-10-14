<?php

namespace App\Form\Admin;

use App\Entity\HistoriqueBlessure;
use App\Entity\Matches;
use App\Entity\Players;
use App\Enum\BlessuresEnum;
use App\Enum\RulesetEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoriqueBlessureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $tableauBlessure = null;

        foreach (BlessuresEnum::numeroToBlessure() as $key => $ligne) {
            $tableauBlessure[$key] = $key . ', ' . $ligne;
        }

        $builder
            ->add('blessure', ChoiceType::class, [
                'choices' => Array_flip($tableauBlessure),
            ])
            ->add('date', DateType::class, ['widget'=>'single_text', 'html5' => true])
            ->add('Player', EntityType::class, [
                'class' => Players::class,
                'choice_label' => fn (Players $joueur) =>  $joueur->getNr()
                    . ', ' . $joueur->getName()
                    . ', ' . ($joueur->getFPos() ? $joueur->getFPos()->getPos() : $joueur->getFPosBb2020()->getPos()) .
                        ($joueur->getJournalier() === true ? ', Journalier' : '')
                    . ', ' . RulesetEnum::numeroVersEtiquette()[$joueur->getRuleset()],
                'group_by' =>  'ownedByTeam.name'
            ])
            ->add('fmatch', EntityType::class, [
                'class' => Matches::class,
                'choice_label' => fn (Matches $match) => $match->getMatchId() . ' - ' .
                        $match->getTeam1()->getName() . ' vs ' .
                        $match->getTeam2()->getName()
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => HistoriqueBlessure::class,
        ]);
    }
}
