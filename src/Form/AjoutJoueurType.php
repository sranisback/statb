<?php

namespace App\Form;

use App\Entity\Teams;
use App\Service\EquipeService;
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
    /**
     * @var \App\Service\PlayerService
     */
    private \App\Service\PlayerService $playerService;

    /**
     * @var EquipeService
     */
    private \App\Service\EquipeService $equipeService;

    public function __construct(PlayerService $playerService, EquipeService $equipeService)
    {
        $this->playerService = $playerService;
        $this->equipeService = $equipeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Teams $equipe */
        $equipe = $options['equipe'];
        $race = $equipe->getRuleset() == 1 ? $equipe->getRace() : $equipe->getFRace();
        $raceId = $equipe->getRuleset() == 1 ? $race->getId() : $race->getRaceId();

        $generateurDeNom = new randomNameGenerator();
        $nom = $generateurDeNom->generateNames(1);

        $numeroAProposer = $this->playerService->numeroLibreDelEquipe($equipe);

        $champ = $equipe->getRuleset() == 1 ? 'race' : 'fRace';
        $champ2 = $equipe->getRuleset() == 1 ? 'fPosBb2020' : 'fPos';

        if (!empty($race)) {
            $builder
                ->add(
                    $champ2,
                    EntityType::class,
                    [
                        'class' => $this->equipeService->getRulesetFromTeamForDataPlayerRepo($equipe),
                        'choice_label' => 'pos',
                        'label' => 'Choisir une position',
                        'query_builder' =>
                            fn(EntityRepository $entityRepository) =>
                            $entityRepository->createQueryBuilder('Position')
                            ->where('Position.' . $champ . ' =' . $raceId),
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'equipe' => 0,
            ]
        );
    }
}
