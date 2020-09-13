<?php

namespace App\Form\admin;

use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Teams;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fans')
            ->add('ffactor1')
            ->add('ffactor2')
            ->add('income1')
            ->add('income2')
            ->add('dateCreated')
            ->add('team1Score')
            ->add('team2Score')
            ->add('tv1')
            ->add('tv2')
            ->add('stadeAcceuil',
                ChoiceType::class,
                [
                    'choices' => ['Equipe 1' => 1, 'Equipe 2 ' => 2, 'Personne' => 3],
                    'label' => 'Stade Acceuil',
                ])
            ->add('depense1')
            ->add('depense2')
            ->add('team1', EntityType::class, ['class' => Teams::class,'choice_label' =>'name'])
            ->add('team2', EntityType::class, ['class' => Teams::class,'choice_label' =>'name'])
            ->add('fMeteo', EntityType::class, ['class' => Meteo::class,'choice_label' =>'nom'])
            ->add('fStade', EntityType::class, ['class' => GameDataStadium::class,'choice_label' =>'type'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matches::class,
        ]);
    }
}
