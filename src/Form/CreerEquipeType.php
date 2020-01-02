<?php

namespace App\Form;

use App\Entity\Races;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreerEquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("Name", TextType::class, ['label'=>"Nom de l'équipe", 'required' => 'true'])
            ->add(
                'fRace',
                EntityType::class,
                ['class'=> Races::class,'choice_label' =>'name','label'=>'Choisir une Race']
            )
            ->add('submit', SubmitType::class, ['label' => 'Créer'])
            ->add('cancel', ButtonType::class, ['label'=>'Annuler','attr'=>['data-dismiss'=>'modal']])
            ->getForm();
    }
}
