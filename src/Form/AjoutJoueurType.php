<?php

namespace App\Form;

use App\Entity\GameDataPlayers;
use App\Entity\Teams;
use App\Service\PlayerService;
use App\Tools\randomNameGenerator;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutJoueurType extends AbstractType
{
    private \App\Service\PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Teams $equipe */
        $equipe = $options['equipe'];
        $race = $equipe->getFRace();

        $generateurDeNom = new randomNameGenerator();
        $nom = $generateurDeNom->generateNames(1);

        $numeroAProposer = $this->playerService->numeroLibreDelEquipe($equipe);

        if (!empty($race)) {
            $builder
                ->add(
                    'fPos',
                    EntityType::class,
                    [
                        'class' => GameDataPlayers::class,
                        'choice_label' => 'pos',
                        'label' => 'Choisir une position',
                        'query_builder' => fn(EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('Position')
                            ->where('Position.fRace ='.$race->getRaceId()),
                        'placeholder' => 'Choisir un joueur',
                        'required' => true
                    ]
                )
                ->add(
                    'name',
                    TextType::class,
                    [
                        'label' => 'Nom du joueur',
                        'required' => true,
                        'empty_data' => $nom[0],
                        'data' => $nom[0]
                    ]
                )
                ->add(
                    'nr',
                    IntegerType::class,
                    [
                        'label' => 'Numero',
                        'empty_data' => $numeroAProposer,
                        'data' => $numeroAProposer,
                        'required' => true
                    ]
                )
                ->add('submit', ButtonType::class, ['label' => 'Ajouter', 'attr' => ['teamId' => $equipe->getTeamId()]])
                ->add('cancel', ButtonType::class, ['label' => 'Quitter', 'attr' => ['data-dismiss' => 'modal']])
                ->getForm();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'equipe' => 0,
            ]
        );
    }
}
