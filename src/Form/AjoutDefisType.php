<?php

namespace App\Form;

use App\Entity\Coaches;
use App\Entity\Teams;
use App\Service\SettingsService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AjoutDefisType extends AbstractType
{
    private settingsService $settingsService;

    private $security;

    public function __construct(Security $security, SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Coaches $coach */
        $coach = $this->security->getUser();

        $builder
            ->add(
                'equipeOrigine',
                EntityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Quelle Equipe ?',
                    'query_builder' =>
                        function (EntityRepository $entityRepository) use($coach) {
                            return $entityRepository->createQueryBuilder('Teams')
                                ->join('Teams.ownedByCoach', 'Coaches')
                                ->where('Teams.year =' . $this->settingsService->anneeCourante())
                                ->andWhere('Teams.ownedByCoach =' . $coach->getCoachId())
                                ->orderBy('Coaches.name', 'ASC');
                        }
                ]
            )
            ->add(
                'equipeDefiee',
                EntityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Defier quelle Equipe/Coach',
                    'query_builder' =>
                        function (EntityRepository $entityRepository) use($coach) {
                            return $entityRepository->createQueryBuilder('Teams')
                                ->join('Teams.ownedByCoach', 'Coaches')
                                ->where('Teams.year =' . $this->settingsService->anneeCourante())
                                ->andWhere('Teams.ownedByCoach !=' . $coach->getCoachId())
                                ->orderBy('Coaches.name', 'ASC');
                            },
                    'group_by' => function (Teams $team) {
                        if (!empty($team->getOwnedByCoach())) {
                            return $team->getOwnedByCoach()->getName();
                        }
                    },
                ]
            )
            ->add('submit', SubmitType::class, array('label' => 'Créer'))
            ->add('cancel', ButtonType::class, array('label' => 'Annuler', 'attr' => array('data-dismiss' => 'modal')));
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'coach' => 0,
            ]
        );
    }
}
