<?php

namespace App\Service;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class MatchesService
{

    private $doctrineEntityManager;
    private $equipeService;
    private $playerService;
    private $settingService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, EquipeService $equipeService, PlayerService $playerService, SettingsService $settingService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->equipeService = $equipeService;
        $this->playerService = $playerService;
        $this->settingService = $settingService;
    }

    public function creationEnteteMatch($donnneesMatch)
    {
        $match = new Matches();

        $team1 = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $donnneesMatch['team_1']]
        );
        $team2 = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $donnneesMatch['team_2']]
        );

        $match->setFans($donnneesMatch['totalpop']);
        $match->setFfactor1($donnneesMatch['varpop_team1']);
        $match->setFfactor2($donnneesMatch['varpop_team2']);
        $match->setIncome1($donnneesMatch['gain1']);
        $match->setIncome2($donnneesMatch['gain2']);
        $match->setTeam1Score($donnneesMatch['score1']);
        $match->setTeam2Score($donnneesMatch['score2']);
        $match->setTeam1($team1);
        $match->setTeam2($team2);
        $match->setTv1($this->equipeService->tvDelEquipe($team1, $this->playerService));
        $match->setTv2($this->equipeService->tvDelEquipe($team2, $this->playerService));

        $match->setDateCreated(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

        $this->doctrineEntityManager->persist($match);

        return $match;
    }

    public function enregistrerMatch($donnneesMatch)
    {
        $match = $this->creationEnteteMatch($donnneesMatch);

        $this->playerService->remplirMatchDataDeLigneAzero($match->getTeam1(),$match);
        $this->playerService->remplirMatchDataDeLigneAzero($match->getTeam2(),$match);

        $this->modificationEquipes($match,$match->getTeam1(),$match->getTeam2());

        $this->playerService->annulerRPMtousLesJoueursDeLequipe($match->getTeam1());
        $this->playerService->annulerRPMtousLesJoueursDeLequipe($match->getTeam2());

        $this->enregistrementDesActionsDesJoueurs($donnneesMatch['player'], $match);

        $this->playerService->controleNiveauDuJoueur($match->getTeam1());
        $this->playerService->controleNiveauDuJoueur($match->getTeam2());

        $this->equipeService->eloDesEquipes($this->settingService->anneeCourante());
    }

    public function modificationEquipes(Matches $match,Teams $equipe1, Teams $equipe2)
    {
        $equipe1->setTreasury($equipe1->getTreasury()+ $match->getIncome1());
        $equipe2->setTreasury($equipe2->getTreasury()+ $match->getIncome2());

        if ($equipe1->getFf() + $match->getFfactor1() < 0) {
            $equipe1->setFf(0);
        } else {
            $equipe1->setFf($equipe1->getFf() + $match->getFfactor1() );
        }

        if ($equipe2->getFf() + $match->getFfactor2() < 0) {
            $equipe2->setFf(0);
        } else {
            $equipe2->setFf($equipe2->getFf() + $match->getFfactor2());
        }

        $this->doctrineEntityManager->persist($equipe1);
        $this->doctrineEntityManager->persist($equipe2);
    }

    public function enregistrementDesActionsDesJoueurs($actionsCollection, Matches $match)
    {
        foreach ($actionsCollection as $action){
            $ligneMatchData = $this->doctrineEntityManager->getRepository(MatchData::class)->findOneBy(
                ['fPlayer' => $action['id'], 'fMatch' => $match->getMatchId()]);

            $joueur = $this->doctrineEntityManager->getRepository(Players::class)->findOneBy(['playerId'=>$action['id']]);

            switch ($action['action']) {
                case 'COMP':
                    $ligneMatchData->setCp(1);
                    break;
                case 'TD':
                    $ligneMatchData->setTd(1);
                    break;
                case 'INT':
                    $ligneMatchData->setIntcpt(1);
                    break;
                case 'CAS - BH':
                    $ligneMatchData->setBh(1);
                    break;
                case 'CAS - SI':
                    $ligneMatchData->setSi(1);
                    break;
                case 'CAS - KI':
                    $ligneMatchData->setKi(1);
                    break;
                case 'MVP':
                    $ligneMatchData->setMvp(1);
                    break;
                case 'AGG':
                    $ligneMatchData->setAgg(1);
                    break;
                case '-1 Ma':
                    $joueur->setInjMa($joueur->getInjMa() + 1);
                    $joueur->setInjRpm(1);
                    break;
                case '-1 St':
                    $joueur->setInjSt($joueur->getInjSt() + 1);
                    $joueur->setInjRpm(1);
                    break;
                case '-1 Ag':
                    $joueur->setInjAg($joueur->getInjAg() + 1);
                    $joueur->setInjRpm(1);
                    break;
                case '-1 Av':
                    $joueur->setInjAv($joueur->getInjAv() + 1);
                    $joueur->setInjRpm(1);
                    break;
                case 'Ni':
                    $joueur->setInjNi($joueur->getInjNi() + 1);
                    $joueur->setInjRpm(1);
                    break;
                case 'RPM':
                    $joueur->setInjRpm(1);
                    break;
                case 'Tué':
                    $joueur->setDateDied(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
                    $joueur->setStatus(8);
                    break;
            }

            $this->doctrineEntityManager->persist($joueur);
            $this->doctrineEntityManager->persist($ligneMatchData);
        }
    }
}