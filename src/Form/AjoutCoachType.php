<?php


namespace App\Form;


use App\Entity\Coaches;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutCoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('Username')
            ->add('Password')
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Coaches::class,
        ]);
    }
}