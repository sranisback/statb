<?php

namespace App\Form;

use App\Entity\Teams;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ->add('ownedByCoach')
            ->add('fRace')
            ->add('fStades')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teams::class,
        ]);
    }
}
