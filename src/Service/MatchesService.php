<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\HistoriqueBlessure;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use App\Entity\ScoreCalcul;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Factory\MatchesFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MatchesService
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;
    /**
     * @var EquipeService
     */
    private EquipeService $equipeService;
    /**
     * @var PlayerService
     */
    private PlayerService $playerService;
    /**
     * @var SettingsService
     */
    private SettingsService $settingService;
    /**
     * @var DefisService
     */
    private DefisService $defisService;
    /**
     * @var InfosService
     */
    private InfosService $infoService;
    /**
     * @var EquipeGestionService
     */
    private EquipeGestionService $equipeGestionService;

    private const DATE_FORMAT = "Y-m-d H:i:s";

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        EquipeService $equipeService,
        PlayerService $playerService,
        SettingsService $settingService,
        DefisService $defisService,
        InfosService $infoService,
        EquipeGestionService $equipeGestionService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->equipeService = $equipeService;
        $this->playerService = $playerService;
        $this->settingService = $settingService;
        $this->defisService = $defisService;
        $this->infoService = $infoService;
        $this->equipeGestionService = $equipeGestionService;
    }

    /**
     * @param array<string,mixed> $donnneesMatch
     * @return array<string,mixed>
     * @throws \Exception
     */
    public function enregistrerMatch(array $donnneesMatch): array
    {
        $match = $this->creationEnteteMatch($donnneesMatch);

        $team1 = $match->getTeam1();
        $team2 = $match->getTeam2();

        if (!empty($team1) && !empty($team2)) {
            $this->processMatch($team1, $match, $team2, $donnneesMatch['player']);
        }

        return ['enregistrement' => $match->getMatchId(), 'defis' => $this->defisService->verificationDefis($match)];
    }

    /**
     * @param array<string,mixed> $donneesMatch
     * @return Matches
     * @throws Exception
     */
    public function creationEnteteMatch(array $donneesMatch): Matches
    {
        /** @var Teams $equipe1 */
        $equipe1 = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $donneesMatch['team_1']]
        );

        $equipe2 = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $donneesMatch['team_2']]
        );

        $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(
            ['id' => $donneesMatch['stade']]
        );

        if ($typeStade == null) {
            $typeStade = new GameDataStadium();
        }

        switch ($donneesMatch['stadeAccueil']) {
            case 1:
                $stade = $equipe1->getFStades();
                if ($stade->getNiveau() === 0) {
                    $typeStade = $stade->getFTypeStade();
                }
                break;
            case 2:
                $stade = $equipe2->getFStades();
                if ($stade->getNiveau() === 0) {
                    $typeStade = $stade->getFTypeStade();
                }
                break;
            default:
                break;
        }

        $match = MatchesFactory::creerUnMatch(
            $donneesMatch,
            $equipe1,
            $equipe2,
            $this->equipeGestionService->tvDelEquipe($equipe1, $this->playerService),
            $this->equipeGestionService->tvDelEquipe($equipe2, $this->playerService),
            $this->doctrineEntityManager->getRepository(Meteo::class)->findOneBy(
                ['id' => $donneesMatch['meteo']]
            ),
            $typeStade
        );
        $this->doctrineEntityManager->persist($match);

        return $match;
    }

    /**
     * @param Matches $match
     * @param Teams $equipe1
     * @param Teams $equipe2
     */
    public function modificationEquipes(Matches $match, Teams $equipe1, Teams $equipe2): void
    {
        $equipe1->setTreasury($equipe1->getTreasury() + $match->getIncome1() + $match->getDepense1());
        $equipe2->setTreasury($equipe2->getTreasury() + $match->getIncome2() + $match->getDepense2());

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
     * @param array<string,mixed> $actionsCollection
     * @param Matches $match
     */
    public function enregistrementDesActionsDesJoueurs(array $actionsCollection, Matches $match): void
    {
        foreach ($actionsCollection as $action) {
            /** @var MatchData $ligneMatchData */
            $ligneMatchData = $this->doctrineEntityManager->getRepository(MatchData::class)->findOneBy(
                ['fPlayer' => $action['id'], 'fMatch' => $match->getMatchId()]
            );

            $joueur = $this->doctrineEntityManager->getRepository(Players::class)->findOneBy(
                ['playerId' => $action['id']]
            );

            $histoBlessure = new HistoriqueBlessure();

            $dateBless = DateTime::createFromFormat(self::DATE_FORMAT, date(self::DATE_FORMAT));

            if (!empty($dateBless)) {
                $histoBlessure->setDate($dateBless);
            }
            $histoBlessure->setFmatch($match);

            switch ($action['action']) {
                case 'LAN':
                    $ligneMatchData->setLan($ligneMatchData->getLan() + 1);
                    break;
                case 'DET':
                    $ligneMatchData->setDet($ligneMatchData->getDet() +1);
                    break;
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
                case 'BONUS XP':
                    $ligneMatchData->setBonusSpp($ligneMatchData->getBonusSpp() + 1);
                    break;
                case 'MVP':
                    $ligneMatchData->setMvp($ligneMatchData->getMvp() + 1);
                    break;
                case 'AGG':
                    $ligneMatchData->setAgg($ligneMatchData->getAgg() + 1);
                    break;
                case 'EXP':
                    $ligneMatchData->setCartonsRouge($ligneMatchData->getCartonsRouge() + 1);
                    break;
                case '-1 Ma':
                    $joueur->setInjMa($joueur->getInjMa() + 1);
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(rand(53, 54), $joueur, $histoBlessure);
                    break;
                case '-1 St':
                    $joueur->setInjSt($joueur->getInjSt() + 1);
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(58, $joueur, $histoBlessure);
                    break;
                case '+1 Ag':
                case '-1 Ag':
                    $joueur->setInjAg($joueur->getInjAg() + 1);
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(57, $joueur, $histoBlessure);
                    break;
                case '+1 Cp':
                    $joueur->setInjCp($joueur->getInjCp() + 1);
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(59, $joueur, $histoBlessure);
                    break;
                case '-1 Av':
                    $joueur->setInjAv($joueur->getInjAv() + 1);
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(rand(55, 56), $joueur, $histoBlessure);
                    break;
                case 'Ni':
                    $joueur->setInjNi($joueur->getInjNi() + 1);
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(rand(51, 52), $joueur, $histoBlessure);
                    break;
                case 'RPM':
                    $joueur->setInjRpm(1);
                    $this->enregistreHistoriqueBlessure(rand(41, 48), $joueur, $histoBlessure);
                    break;
                case 'Tué':
                    $date = DateTime::createFromFormat(self::DATE_FORMAT, date(self::DATE_FORMAT));
                    if (!empty($date)) {
                        $joueur->setDateDied($date);
                    }
                    $joueur->setStatus(8);
                    $histoBlessure->setBlessure(60);
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    $this->infoService->mortDunJoueur($joueur);
                    break;
                case 'COMO':
                    $histoBlessure->setBlessure(30);
                    $joueur->addHistoriqueBlessure($histoBlessure);
                    break;
                default:
                    break;
            }

            if ($histoBlessure->getPlayer() !== null) {
                $this->doctrineEntityManager->persist($histoBlessure);
            }
            $this->doctrineEntityManager->persist($joueur);
            $this->doctrineEntityManager->persist($ligneMatchData);
        }
    }

    private function enregistreHistoriqueBlessure(int $nbr, Players $joueur, HistoriqueBlessure $histoBlessure) : void
    {
        $histoBlessure->setBlessure($nbr);
        $joueur->addHistoriqueBlessure($histoBlessure);
    }

    /**
     * @param Coaches|Object $coach
     * @return mixed[][]
     */
    public function tousLesMatchesDunCoachParAnnee($coach): array
    {
        $anneeEnCours = $this->settingService->anneeCourante();
        $anneeEtiquette = AnneeEnum::numeroToAnnee();

        $equipesParAnnees = [];
        $liste = [];

        for ($x = 0; $x < $anneeEnCours; $x++) {
            $equipesParAnnees[$x] = $this->doctrineEntityManager->getRepository(
                Teams::class
            )->toutesLesEquipesDunCoachParAnnee($coach, $x);
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

    /**
     * @param Teams $team1
     * @param Matches $match
     * @param Teams $team2
     * @param $player
     */
    private function processMatch(Teams $team1, Matches $match, Teams $team2, $player): void
    {
        $this->playerService->remplirMatchDataDeLigneAzero($team1, $match);

        $this->playerService->remplirMatchDataDeLigneAzero($team2, $match);

        $this->modificationEquipes($match, $team1, $team2);

        $this->playerService->annulerRPMtousLesJoueursDeLequipe($team1);
        $this->playerService->annulerRPMtousLesJoueursDeLequipe($team2);

        $this->enregistrementDesActionsDesJoueurs($player, $match);

        $this->playerService->controleNiveauDesJoueursDelEquipe($team1);
        $this->playerService->controleNiveauDesJoueursDelEquipe($team2);

        $this->equipeService->eloDesEquipes($this->settingService->anneeCourante());

        $this->majScoreDesEquipes($match, $team1);
        $this->majScoreDesEquipes($match, $team2);

        $this->infoService->matchEnregistre($match);
    }

    private function majScoreDesEquipes(Matches $match, Teams $equipe)
    {
        $points = $this->settingService->pointsEnCours($this->settingService->anneeCourante());

        $resultat = $this->equipeService->resultatDuMatch($equipe,$match);

        $pointsDuMatch = 0;

        $pointsDuMatch += $resultat['win'] == 1 ? $points[0] : 0;
        $pointsDuMatch += $resultat['draw'] == 1 ? $points[1] : 0;
        $pointsDuMatch += $resultat['loss'] == 1 ? $points[2] : 0;

        list($bonus, $pointsPerdus) = $this->equipeService->calculBonusPourUnMatchPoulpi($equipe, $match);

        $bonus = $this->equipeService->maxScore($bonus, $match, $equipe);

        $equipe->setScore($equipe->getScore() + $pointsDuMatch + $bonus);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $scoreCalcul = new ScoreCalcul();
        $scoreCalcul->setTeams($equipe);
        $scoreCalcul->setMatchCible($match);
        $scoreCalcul->setBonus($bonus);
        $scoreCalcul->setLostPoint($pointsPerdus);

        $this->doctrineEntityManager->persist($scoreCalcul);
        $this->doctrineEntityManager->flush();

    }

    public function recalculLeScore()
    {
        $scoreCalculArray = $this->doctrineEntityManager->getRepository(ScoreCalcul::class)->findAll();
        foreach ($scoreCalculArray as $scoreCalcul) {
            $this->doctrineEntityManager->remove($scoreCalcul);
        }
        $this->doctrineEntityManager->flush();

        $equipeList = $this->doctrineEntityManager->getRepository(Teams::class)->findBy(['year' => $this->settingService->anneeCourante()]);

        array_map(function ($equipe) { return $equipe->setScore(100);}, $equipeList);

        $matchList = $this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnneClassementChrono($this->settingService->anneeCourante(), 'ASC');

        /** @var Matches $match */
        foreach ($matchList as $match) {
            $match->setScoreClassementTeam1($match->getTeam1()->getScore());
            $match->setScoreClassementTeam2($match->getTeam2()->getScore());

            $this->majScoreDesEquipes($match, $match->getTeam1());
            $this->majScoreDesEquipes($match, $match->getTeam2());
        }
    }

}
