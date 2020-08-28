<?php

namespace App\Form\AdminForm;

use App\Entity\Players;
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
           // ->add('fPos')
           // ->add('fRid')
           // ->add('ownedByTeam')
          //  ->add('icon')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Players::class,
        ]);
    }
}
