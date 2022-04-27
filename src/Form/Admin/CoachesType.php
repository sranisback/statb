<?php

namespace App\Form\Admin;

use App\Entity\Coaches;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoachesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('Username')
            ->add('Password')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => ['Utilisateur' => 'USER', 'Admin' => 'ADMIN'],
                    'mapped' => false,
                    'label' => 'Role',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Coaches::class,
        ]);
    }
}
