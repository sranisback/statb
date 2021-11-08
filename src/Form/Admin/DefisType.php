<?php

namespace App\Form\Admin;

use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('defieRealise')
            ->add('dateDefi', DateType::class, ['widget'=>'single_text', 'html5' => true])
            ->add('equipeDefiee', EntityType::class, ['class' => Teams::class, 'choice_label' =>'name'])
            ->add(
                'matchDefi',
                EntityType::class,
                [
                    'class' => Matches::class,
                    'choice_label' => fn (Matches $match) =>  $match->getMatchId() . ' - ' .
                        ($match->getTeam1() ? $match->getTeam1()->getName() : '') . ' vs ' .
                        ($match->getTeam2() ? $match->getTeam2()->getName() : '')
                ]
            )
            ->add('equipeOrigine', EntityType::class, ['class' => Teams::class, 'choice_label' =>'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Defis::class,
        ]);
    }
}
