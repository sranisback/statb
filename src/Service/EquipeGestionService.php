<?php


namespace App\Service;


use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataStadium;
use App\Entity\Players;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Factory\PlayerFactory;
use App\Factory\TeamsFactory;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class EquipeGestionService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    /**
     * @var SettingsService
     */
    private SettingsService $settingsService;

    /**
     * @var InfosService
     */
    private InfosService $infoService;

    /**
     * @var InducementService
     */
    private InducementService $inducementService;

    private const BASE_ELO = 150;

    private const MORTS_VIVANTS = 'Morts vivants';
    private const OWA = 'OWA';
    private const BAS_FOND = 'Bas fonds';

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        SettingsService $settingsService,
        InfosService $infoService,
        InducementService $inducementService
    ) {
        $this->infoService = $infoService;
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->settingsService = $settingsService;
        $this->inducementService = $inducementService;
    }

    /**
     * @param int $ruleset
     * @param int $raceid
     * @param int $coachid
     * @param string $teamname
     * @return int|null
     */
    public function creationEquipe(int $ruleset, int $raceid, int $coachid, string $teamname): ?int
    {
        $race = $this->doctrineEntityManager->getRepository(RulesetEnum::getRaceRepoFromIntByRuleset($ruleset))
            ->findOneBy([RulesetEnum::getRaceIdFromIntByRuleset($ruleset) => $raceid]);
        $coach = $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(['coachId' => $coachid]);

        $stade = new Stades();
        $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(['id' => 0]);

        $stade->setFTypeStade($typeStade);
        $stade->setTotalPayement(0);
        $stade->setNom('La prairie verte');
        $stade->setNiveau(0);
        $this->doctrineEntityManager->persist($stade);

        $equipe = TeamsFactory::lancerEquipe(
            $this->settingsService->recupererTresorDepart(),
            $teamname,
            self::BASE_ELO,
            $stade,
            $this->settingsService->anneeCourante(),
            /* @phpstan-ignore-next-line */
            $race,
            $coach,
            $ruleset
        );

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $this->infoService->equipeEstCree($equipe);

        $equipeId = $equipe->getTeamId();

        if (!empty($equipeId)) {
            return $equipeId;
        }

        return null;
    }

    /**
     * @param Teams $equipe
     */
    public function mettreEnFranchise(Teams $equipe) : void
    {
        if ($equipe->getFranchise() === false) {
            $equipe->setFranchise(true);
        } else {
            $equipe->setFranchise(false);
        }

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     */
    public function checkEquipe(Teams $equipe, PlayerService $playerService): void
    {
        $playerService->controleNiveauDesJoueursDelEquipe($equipe);

        $this->gestionDesJournaliers($equipe, $playerService);

        $equipe->setTv($this->tvDelEquipe($equipe, $playerService));

        $this->doctrineEntityManager->persist($equipe);

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return int[]|array
     */
    public function gestionDesJournaliers(Teams $equipe, PlayerService $playerService): array
    {
        $resultat = [];

        $nbrJoueurActifs = count(
            $this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
                $equipe
            )
        );

        if ($nbrJoueurActifs > 11) {
            $resultat['vendu'] = $this->suppressionDesJournaliers($nbrJoueurActifs - 11, $equipe);
        } elseif ($nbrJoueurActifs < 11) {
            $resultat['ajout'] = $this->ajoutDesJournaliers(
                11 - $nbrJoueurActifs,
                $equipe,
                $playerService
            );
        }

        return $resultat;
    }

    /**
     * @param int $nbrDeJournalierAvendre
     * @param Teams $equipe
     * @return int
     */
    public function suppressionDesJournaliers(int $nbrDeJournalierAvendre, Teams $equipe): int
    {
        $nombreVendu = 0;

        foreach ($this->doctrineEntityManager->getRepository(
            Players::class
        )->listeDesJournaliersDeLequipe($equipe) as $journalierAVendre) {
            /** @var Players $journalierAVendre */
            if ($nombreVendu < $nbrDeJournalierAvendre && $journalierAVendre->getStatus() != 9) {
                $journalierAVendre->setStatus(7);
                $dateSoldFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
                if ($dateSoldFormat) {
                    $journalierAVendre->setDateSold($dateSoldFormat);
                }
                $this->doctrineEntityManager->persist($journalierAVendre);
                $this->doctrineEntityManager->flush();
                $nombreVendu++;
            }
        }

        return $nombreVendu;
    }


    /**
     * @param int $nbrDeJournalier
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return int
     */
    public function ajoutDesJournaliers(int $nbrDeJournalier, Teams $equipe, PlayerService $playerService): int
    {
        $nombreAjoute = 0;

        /** @var GameDataPlayers $positionJournalier */
        $positionJournalier = $this->positionDuJournalier($equipe);

        for ($x = 0; $x < $nbrDeJournalier; $x++) {
            $journalier = PlayerFactory::nouveauJoueur(
                $positionJournalier,
                $playerService->numeroLibreDelEquipe($equipe),
                $equipe,
                true,
                $this->doctrineEntityManager,
                $equipe->getRuleset()
            );

            $journalier->setOwnedByTeam($equipe);

            $this->doctrineEntityManager->persist($journalier);

            $this->doctrineEntityManager->flush();

            $nombreAjoute++;
        }

        return $nombreAjoute;
    }

    /**
     * @param Teams $equipe
     * @return GameDataPlayersBb2020|GameDataPlayers|null
     */
    public function positionDuJournalier(Teams $equipe)
    {
        $race = RulesetEnum::getRaceFromEquipeByRuleset($equipe);

        if ($equipe->getRuleset() == RulesetEnum::BB_2016) {
            return $this->getPositionJournalierBb2016($race, $equipe);
        }

        if ($equipe->getRuleset() == RulesetEnum::BB_2020) {
            return $this->getPositionJournalierBb2020($race, $equipe);
        }

        return null;
    }


    /**
     * @param $race
     * @param Teams $equipe
     * @return GameDataPlayers|null
     */
    private function getPositionJournalierBb2016($race, Teams $equipe): ?GameDataPlayers
    {
        switch ($race->getName()) {
            case self::MORTS_VIVANTS:
                return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)
                    ->findOneBy(['posId' => '171']);
            case self::OWA:
            case self::BAS_FOND:
                return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)->findOneBy(
                    ['fRace' => $equipe->getFRace(), 'qty' => '12']
                );
            default:
                return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)->findOneBy(
                    ['fRace' => $equipe->getFRace(), 'qty' => '16']
                );
        }
    }

    /**
     * @param $race
     * @param Teams $equipe
     * @return GameDataPlayersBb2020|null
     */
    private function getPositionJournalierBb2020($race, Teams $equipe): ?GameDataPlayersBb2020
    {
        if ($race->getName() == self::MORTS_VIVANTS) {
            return $this->doctrineEntityManager->getRepository(GameDataPlayersBb2020::class)
                ->findOneBy(['id' => '74']);
        }
        $position = $this->doctrineEntityManager->getRepository(GameDataPlayersBb2020::class)->findOneBy(
            ['race' => $equipe->getRace(), 'qty' => '12']
        );
        if (!$position) {
            return $this->doctrineEntityManager->getRepository(GameDataPlayersBb2020::class)->findOneBy(
                ['race' => $equipe->getRace(), 'qty' => '16']
            );
        }
        return $position;
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return int
     */
    public function tvDelEquipe(Teams $equipe, PlayerService $playerService) : int
    {
        $coutTotalJoueur = $playerService->coutTotalJoueurs($equipe);

        $inducement = $this->inducementService->valeurInducementDelEquipe($equipe);

        return $coutTotalJoueur + $inducement['total'];
    }

}