<?php

namespace App\Service;

use App\Entity\GameDataStadium;
use App\Entity\HistoriqueBlessure;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Factory\MatchesFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;

class MatchesService
{

    private $doctrineEntityManager;
    private $equipeService;
    private $playerService;
    private $settingService;
    private $defisService;

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        EquipeService $equipeService,
        PlayerService $playerService,
        SettingsService $settingService,
        DefisService $defisService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->equipeService = $equipeService;
        $this->playerService = $playerService;
        $this->settingService = $settingService;
        $this->defisService = $defisService;
    }

    /**
     * @param array $donnneesMatch
     * @return array
     * @throws \Exception
     */
    public function enregistrerMatch(array $donnneesMatch)
    {
        $match = $this->creationEnteteMatch($donnneesMatch);

        $team1 = $match->getTeam1();
        $team2 = $match->getTeam2();

        if (!empty($team1) && !empty($team2)) {
            $this->playerService->remplirMatchDataDeLigneAzero($team1, $match);

            $this->playerService->remplirMatchDataDeLigneAzero($team2, $match);

            $this->modificationEquipes($match, $team1, $team2);

            $this->playerService->annulerRPMtousLesJoueursDeLequipe($team1);
            $this->playerService->annulerRPMtousLesJoueursDeLequipe($team2);

            $this->enregistrementDesActionsDesJoueurs($donnneesMatch['player'], $match);

            $this->playerService->controleNiveauDesJoueursDelEquipe($team1);
            $this->playerService->controleNiveauDesJoueursDelEquipe($team2);

            $this->equipeService->eloDesEquipes($this->settingService->anneeCourante());
        }

        return ['enregistrement' => $match->getMatchId(), 'defis' => $this->defisService->verificationDefis($match)];
    }

    /**
     * @param array $donneesMatch
     * @return Matches
     */
    public function creationEnteteMatch(array $donneesMatch)
    {
        $equipe1 = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $donneesMatch['team_1']]
        );

        $equipe2 = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $donneesMatch['team_2']]
        );

        $match = (new MatchesFactory)->creerUnMatch(
            $donneesMatch,
            $equipe1,
            $equipe2,
            $this->equipeService->tvDelEquipe($equipe1, $this->playerService),
            $this->equipeService->tvDelEquipe($equipe2, $this->playerService),
            $this->doctrineEntityManager->getRepository(Meteo::class)->findOneBy(
                ['id' => $donneesMatch['meteo']]
            ),
            $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(
                ['id' => $donneesMatch['stade']]
            )
        );

        $this->doctrineEntityManager->persist($match);

        return $match;
    }

    /**
     * @param Matches $match
     * @param Teams $equipe1
     * @param Teams $equipe2
     */
    public function modificationEquipes(Matches $match, Teams $equipe1, Teams $equipe2)
    {
        $equipe1->setTreasury($equipe1->getTreasury() + $match->getIncome1());
        $equipe2->setTreasury($equipe2->getTreasury() + $match->getIncome2());

        if ($equipe1->getFf() + $match->getFfactor1() < 0) {
            $equipe1->setFf(0);
        } else {
            $equipe1->setFf($equipe1->getFf() + $match->getFfactor1());
        }

        if ($equipe2->getFf() + $match->getFfactor2() < 0) {
            $equipe2->setFf(0);
        } else {
            $equipe2->setFf($equipe2->getFf() + $match->getFfactor2());
        }

        $this->doctrineEntityManager->persist($equipe1);
        $this->doctrineEntityManager->persist($equipe2);
    }

    /**
     * @param array $actionsCollection
     * @param Matches $match
     */
    public function enregistrementDesActionsDesJoueurs(array $actionsCollection, Matches $match)
    {
        foreach ($actionsCollection as $action) {
            $ligneMatchData = $this->doctrineEntityManager->getRepository(MatchData::class)->findOneBy(
                ['fPlayer' => $action['id'], 'fMatch' => $match->getMatchId()]
            );

            $joueur = $this->doctrineEntityManager->getRepository(Players::class)->findOneBy(
                ['playerId' => $action['id']]
            );

            $histoBlessure = new HistoriqueBlessure();

            $dateBless = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

            if (!empty($dateBless)) {
                $histoBlessure->setDate($dateBless);
            }
            $histoBlessure->setFmatch($match);

            switch ($action['action']) {
                case 'COMP':
                    $ligneMatchData->setCp($ligneMatchData->getCp() + 1);
                    break;
                case 'TD':
                    $ligneMatchData->setTd($ligneMatchData->getTd() + 1);
                    break;
                case 'INT':
                    $ligneMatchData->setIntcpt($ligneMatchData->getIntcpt() + 1);
                    break;
                case 'CAS - BH':
                    $ligneMatchData->setBh($ligneMatchData->getBh() + 1);
                    break;
                case 'CAS - SI':
                    $ligneMatchData->setSi($ligneMatchData->getSi() + 1);
                    break;
                case 'CAS - KI':
                    $ligneMatchData->setKi($ligneMatchData->getKi() + 1);
                    break;
                case 'MVP':
                    $ligneMatchData->setMvp($ligneMatchData->getMvp() + 1);
                    break;
                case 'AGG':
                    $ligneMatchData->setAgg($ligneMatchData->getAgg() + 1);
                    break;
                case '-1 Ma':
                    $joueur->setInjMa($joueur->getInjMa() + 1);
                    $joueur->setInjRpm(1);
                    $histoBlessure->setBlessure(rand(53, 54));
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case '-1 St':
                    $joueur->setInjSt($joueur->getInjSt() + 1);
                    $joueur->setInjRpm(1);
                    $histoBlessure->setBlessure(58);
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case '-1 Ag':
                    $joueur->setInjAg($joueur->getInjAg() + 1);
                    $joueur->setInjRpm(1);
                    $histoBlessure->setBlessure(57);
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case '-1 Av':
                    $joueur->setInjAv($joueur->getInjAv() + 1);
                    $joueur->setInjRpm(1);
                    $histoBlessure->setBlessure(rand(55, 56));
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case 'Ni':
                    $joueur->setInjNi($joueur->getInjNi() + 1);
                    $joueur->setInjRpm(1);
                    $histoBlessure->setBlessure(rand(51, 52));
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case 'RPM':
                    $joueur->setInjRpm(1);
                    $histoBlessure->setBlessure(rand(41, 48));
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case 'Tué':
                    $joueur->setDateDied(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
                    $joueur->setStatus(8);
                    $histoBlessure->setBlessure(60);
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                case 'COMO':
                    $histoBlessure->setBlessure(30);
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
            }

            if ($histoBlessure->getPlayer()) {
                $this->doctrineEntityManager->persist($histoBlessure);
            }
            $this->doctrineEntityManager->persist($joueur);
            $this->doctrineEntityManager->persist($ligneMatchData);
        }
    }

    /**
     * @param int $coachId
     * @return mixed
     */
    public function tousLesMatchesDunCoachParAnnee(int $coachId)
    {
        $anneeEnCours = $this->settingService->anneeCourante();
        $anneeEtiquette = (new AnneeEnum)->numeroToAnnee();

        $equipesParAnnees = [];
        $liste = [];

        for ($x = 0; $x < $anneeEnCours; $x++) {
            $equipesParAnnees[$x] = $this->doctrineEntityManager->getRepository(
                Teams::class
            )->toutesLesEquipesDunCoachParAnnee($coachId, $x);
        }

        foreach ($equipesParAnnees as $nbrAnnee => $listeEquipeDelAnnee) {
            $liste[$anneeEtiquette[$nbrAnnee]] = [];
            if (!empty($listeEquipeDelAnnee)) {
                foreach ($listeEquipeDelAnnee as $equipe) {
                    $liste[$anneeEtiquette[$nbrAnnee]] = array_merge(
                        $liste[$anneeEtiquette[$nbrAnnee]],
                        $this->doctrineEntityManager->getRepository(
                            Matches::class
                        )->listeDesMatchs($equipe)
                    );
                }
            }
        }

        return $liste;
    }
}
