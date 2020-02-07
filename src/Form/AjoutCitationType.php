<?php

namespace App\Form;

use App\Entity\Coaches;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AjoutCitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('citation', TextType::class)
            ->add('coachId', EntityType::class, [
                'class' => Coaches::class,
                'choice_label' => 'name',
                'label' => 'Coach'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']])
            ->getForm();
    }
}
