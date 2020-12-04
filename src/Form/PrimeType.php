<?php

namespace App\Form;

use App\Entity\Players;
use App\Entity\Primes;
use App\Service\SettingsService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrimeType extends AbstractType
{
    private SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
         $this->settingsService =  $settingsService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add(
                'players',
                EntityType::class,
                [
                    'class' => Players::class,
                    'choice_label' => function (Players $joueur) {
                        $nomDuJoueur = 'Inconnu';
                        if (!empty($joueur->getName()) && strlen($joueur->getName()) != 2) {
                            $nomDuJoueur = $joueur->getName();
                        }

                        if (!empty($joueur)) {
                            $position = $joueur->getFPos();
                            if (!empty($position)) {
                                return $joueur->getNr().'. '.$nomDuJoueur.', '.$position->getPos();
                            }
                        }
                        return '';
                    },
                    'label' => 'Choisir un joueur',
                    'query_builder' => fn (EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('players')
                            ->join('players.ownedByTeam', 'teams')
                            ->where('teams.year ='.$this->settingsService->anneeCourante())
                            ->andWhere('players.journalier = FALSE')
                            ->andWhere('players.status = 1 OR players.status = 9')
                            ->orderBy('players.nr'),
                    'group_by' => function (Players $joueur) {
                        $equipe = $joueur->getOwnedByTeam();
                        if (!empty($equipe)) {
                            $raceEquipe = $equipe->getFRace();
                            $coach = $equipe->getOwnedByCoach();
                        }

                        if (!empty($joueur) && !empty($raceEquipe) && !empty($equipe) && !empty($coach)) {
                            return $equipe->getName().', '.$raceEquipe->getName().', '.$coach->getName();
                        }
                        return '';
                    },
                    'placeholder' => 'Choisir un joueur',
                ]
            )
            ->add(
                'montant',
                IntegerType::class,
                ['label' => 'Montant de la prime', 'data' => '0', 'attr' => ['step' => 10_000, 'min' => 0]]
            )
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']])
            ->getForm();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Primes::class,
            ]
        );
    }
}
