<?php

namespace App\form;


use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AjoutMatchType extends AbstractType
{
    private $equipeService;
    private $settingsService;
    private $playerService;

    public function __construct(EquipeService $equipeService, SettingsService $settingsService, PlayerService $playerService )
    {
        $this->equipeService = $equipeService;
        $this->playerService = $playerService;
        $this->settingsService = $settingsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $playerService = $this->playerService;
        $builder
            ->add('income1',null,['label'=>'gain Equipe 1','empty_data'=>0,'data'=>'0','attr'=>['step'=>10000,'min'=>0]])
            ->add('income2',null,['label'=>'gain Equipe 2','empty_data'=>0,'data'=>'0','attr'=>['step'=>10000,'min'=>0]])
            ->add('team1_score',null,['label'=>'Score Equipe 1','empty_data'=>0,'data'=>'0','attr'=>['min'=>0]])
            ->add('team2_score',null,['label'=>'Score Equipe 2','empty_data'=>0,'data'=>'0','attr'=>['min'=>0]])
            ->add('team1',EntityType::class,[
                'class' => Teams::class,
                'query_builder'=>function(EntityRepository $entityRepository)  {
                    return $entityRepository->createQueryBuilder('t')
                        ->where('t.year =' . $this->settingsService->anneeCourante())
                        ->orderBy('t.name');
                },
                'choice_label'=>function($equipe) use ($playerService) {
                    /** @var Teams $equipe */
                    $coach = $equipe->getOwnedByCoach();
                    $race = $equipe->getFRace();
                    return $equipe->getName().' ('.$race->getName().') '.($this->equipeService->tvDelEquipe($equipe, $playerService)/1000).' de '.$coach->getName();
                }
            ]);

    }
}