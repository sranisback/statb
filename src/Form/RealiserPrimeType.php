<?php

namespace App\Form;

use App\Entity\Primes;
use App\Entity\Teams;
use App\Service\SettingsService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealiserPrimeType extends AbstractType
{
    private settingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'Primes',
                EntityType::class,
                [
                    'class' => Primes::class,
                    'query_builder' =>
                        fn(EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('Primes')
                            ->join('Primes.players', 'Players')
                            ->join('Players.ownedByTeam', 'Teams')
                        ->where('Primes.equipePrime IS NULL')
                        ->andWhere('Teams.year ='. $this->settingsService->anneeCourante()),
                    'choice_label' => function (Primes $prime) {
                        $joueur = $prime->getPlayers();
                        if (!empty($joueur)) {
                            $equipe = $joueur->getOwnedByTeam();
                            if (!empty($equipe)) {
                                return $prime->getMontant().'PO  de '.$equipe->getName();
                            }
                        }
                        return '';
                    },
                    'group_by' => function (Primes $prime) {
                        $nomDuJoueur = 'Inconnu';

                        $joueur = $prime->getPlayers();
                        if (!empty($joueur)) {
                            $positionJoueur = $joueur->getFPos();
                            $equipe = $joueur->getOwnedByTeam();
                            if (!empty($joueur->getName()) && strlen($joueur->getName()) != 2) {
                                $nomDuJoueur = $joueur->getName();
                            }
                        }

                        if (!empty($joueur) && !empty($positionJoueur) && !empty($equipe)) {
                            return $joueur->getNr().'. '.$nomDuJoueur.', '.$positionJoueur->getPos(
                            ).' de '.$equipe->getName();
                        }
                        return '';
                    },
                    'label' => 'Choisir une Prime',
                    'mapped' => false,
                ]
            )
            ->add('Teams', EntityType::class,             [
                'class' => Teams::class,
                'choice_label' => 'name',
                'label' => 'Choisir une Equipe',
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'Realiser'])
            ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']])
            ->getForm();
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'year' => 0,
            ]
        );
    }
}
