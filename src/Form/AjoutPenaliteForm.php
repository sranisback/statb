<?php

namespace App\Form;

use App\Entity\Penalite;
use App\Entity\Teams;
use App\Service\SettingsService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutPenaliteForm extends AbstractType
{
    private settingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('points')
            ->add('motif')
            ->add(
                'equipe',
                EntityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Quelle Equipe ?',
                    'query_builder' =>
                        fn(EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('Teams')
                        ->where('Teams.year ='.$this->settingsService->anneeCourante())
                        ->orderBy('Teams.name', 'ASC'),
                ]
            )
            ->add('submit', SubmitType::class, array('label' => 'Ajouter'))
            ->add('cancel', ButtonType::class, array('label' => 'Annuler', 'attr' => array('data-dismiss' => 'modal')));
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Penalite::class,
        ]);
    }
}
