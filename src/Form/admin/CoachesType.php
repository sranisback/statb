<?php

namespace App\Form\admin;

use App\Entity\Coaches;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoachesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('passwd')
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Coaches::class,
        ]);
    }
}
