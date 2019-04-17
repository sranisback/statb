<?php


namespace App\Service;

use App\Entity\GameDataPlayers;
use App\Entity\MatchData;

use App\Entity\Matches;
use App\Entity\Teams;

use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PlayerService
{
    private $doctrineEntityManager;
    private $equipeService;
    private $matchDataService;

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        EquipeService $equipeService,
        MatchDataService $matchDataService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->equipeService = $equipeService;
        $this->matchDataService = $matchDataService;
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesJoueursDelEquipe($equipe)
    {
        return $this->doctrineEntityManager->getRepository(Players::class)->findBy(
            ['ownedByTeam' => $equipe->getTeamId()],
            ['nr' => 'ASC']
        );
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesJoueursActifsDelEquipe(Teams $equipe)
    {
        $joueurActifCollection = [];

        $playerCollection = $this->doctrineEntityManager->getRepository(Players::class)->findBy(
            ['ownedByTeam' => $equipe->getTeamId()],
            ['nr' => 'ASC']
        );

        foreach ($playerCollection as $player) {
            if ($player->getStatus()!=7 && $player->getStatus()!=8) {
                $joueurActifCollection[] = $player;
            }
        }

        return $joueurActifCollection;
    }

    public function remplirMatchDataDeLigneAzero(Teams $equipe, Matches $match)
    {
        foreach ($this->listeDesJoueursActifsDelEquipe($equipe) as $joueur) {
            $this->matchDataService->creationLigneVideDonneeMatch($joueur, $match);
        }
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function statsDuJoueur($joueur)
    {
        $toutesLesCompsDuJoueur = $this->toutesLesCompsdUnJoueur($joueur);

        $actions = $this->actionsDuJoueur($joueur);

        $toutesLesCompsDuJoueur = substr($toutesLesCompsDuJoueur, 0, strlen($toutesLesCompsDuJoueur) - 2);

        return ['comp'=>$toutesLesCompsDuJoueur, 'actions'=>$actions];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function toutesLesCompsdUnJoueur($joueur)
    {
        $toutesLesCompsDuJoueur = $this->listeDesCompdDeBasedUnJoueur($joueur);
        $compsupp = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);

        $toutesLesCompsDuJoueur .= $compsupp['compgagnee'];

        $statpec = $this->listenivSpeciauxEtSurcout($joueur);

        $toutesLesCompsDuJoueur .= $statpec['nivspec'];

        if ($joueur->getType()!= 1) {
            $toutesLesCompsDuJoueur .= '<text class="text-danger">Loner</text>, ';
        }

        return $toutesLesCompsDuJoueur;
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function listeDesCompdDeBasedUnJoueur(Players $joueur)
    {
        $position  = $joueur->getFPos();

        if ($position) {
            return $this->listeDesCompdUnePosition($position);
        }
        return '';
    }

    /**
     * @param GameDataPlayers $position
     * @return string
     */
    public function listeDesCompdUnePosition(GameDataPlayers $position)
    {
        if ($position->getSkills() != '') {
            $idcompCollection = explode(",", (string) $position->getSkills());
        }

        $listeCompDeBase = '';

        if (!empty($idcompCollection)) {
            foreach ($idcompCollection as $idComp) {
                $comp = $this->doctrineEntityManager->getRepository(GameDataSkills::class)->findOneBy(
                    ['skillId' => $idComp]
                );

                $listeCompDeBase .= '<text class="test-primary">'.$comp->getName().'</text>, ';
            }
            return $listeCompDeBase;
        }
        return '';
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function listeDesCompEtSurcoutGagnedUnJoueur(Players $joueur)
    {
        $compSupplementaire = $this->doctrineEntityManager->getRepository(PlayersSkills::class)->findBy(
            ['fPid' => $joueur->getPlayerId()]
        );

        $coutTotal = 0;
        $listCompGagnee = '';

        if ($compSupplementaire) {
            foreach ($compSupplementaire as $comp) {
                if ($comp->getType() == 'N') {
                    $coutTotal += 20000;
                    $listCompGagnee .= '<text class="text-success">'.$comp->getFSkill()->getName().'</text>, ';
                } else {
                    $coutTotal += 30000;
                    $listCompGagnee .= '<text class="text-danger">'.$comp->getFSkill()->getName().'</text>, ';
                }
            }
        }
        return ['compgagnee'=>$listCompGagnee,'cout'=> $coutTotal];
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function listenivSpeciauxEtSurcout(Players $joueur)
    {
        $listSupp = '';
        $cout = 0;

        if ($joueur->getInjNi() > 0) {
            $listSupp .= '<text class="text-danger">+1 Ni</text>, ';
        }

        if ($joueur->getAchMa() > 0) {
            $listSupp .= '<text class="text-success">+1 Ma</text>, ';
            $cout += 30000;
        }

        if ($joueur->getAchSt() > 0) {
            $listSupp .= '<text class="text-success">+1 St</text>, ';

            $cout += 50000;
        }

        if ($joueur->getAchAg() > 0) {
            $listSupp .= '<text class="text-success">+1 Ag</text>, ';

            $cout += 40000;
        }

        if ($joueur->getAchAv() > 0) {
            $listSupp .= '<text class="text-success">+1 Av</text>, ';

            $cout += 30000;
        }

        return ['nivspec'=>$listSupp,'cout'=> $cout];
    }

    /**
     * @param Players  $joueur
     * @return array
     */
    public function actionsDuJoueur(Players $joueur)
    {
        $mdata = $this->doctrineEntityManager->getRepository(MatchData::class)->findBy(
            ['fPlayer' => $joueur->getPlayerId()]
        );

        $tcp = 0;
        $ttd = 0;
        $tint = 0;
        $tcas = 0;
        $tmvp = 0;
        $tagg = 0;
        $tMatch = 0;

        foreach ($mdata as $game) {
            $tcp += $game->getCp();
            $ttd += $game->getTd();
            $tint += $game->getIntcpt();
            $tcas += ($game->getBh() + $game->getSi() + $game->getKi());
            $tmvp += $game->getMvp();
            $tagg += $game->getAgg();
            $tMatch++;
        }

        return ['NbrMatch'=>$tMatch, 'cp'=> $tcp, 'td'=>$ttd,'int'=> $tint,'cas'=> $tcas,'mvp' => $tmvp,'agg'=> $tagg];
    }

    /**
     * @param Players $joueur
     * @return float|int
     */
    public function xpDuJoueur(Players $joueur)
    {

        $actions = $this->actionsDuJoueur($joueur);

        return $actions['cp'] + ($actions['td'] * 3)
            + ($actions['int'] * 2) + ($actions['cas'] * 2) + ($actions['mvp'] * 5);
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function statutDuJoueur(Players $joueur)
    {
        switch ($joueur->getStatus()) {
            case 7:
                return 'VENDU';

            case 8:
                return 'MORT';

            case 9:
                return 'XP';

            default:
                if ($joueur->getInjRpm() != 0) {
                    return 'RPM';
                } else {
                    return '';
                }
        }
    }

    /**
     * @param MatchData $matchData
     * @return array
     */
    public function actionDuJoueurDansUnMatch(MatchData $matchData)
    {
        $tcp = 0;
        $ttd = 0;
        $tint = 0;
        $tcas = 0;
        $tmvp = 0;
        $tagg = 0;


        $tcp += $matchData->getCp();
        $ttd += $matchData->getTd();
        $tint += $matchData->getIntcpt();
        $tcas += ($matchData->getBh() + $matchData->getSi() + $matchData->getKi());
        $tmvp += $matchData->getMvp();
        $tagg += $matchData->getAgg();

        $rec = '';

        if ($matchData->getCp() > 0 || $matchData->getTd() > 0 || $matchData->getIntcpt() > 0
            || ($matchData->getBh() + $matchData->getSi() + $matchData->getKi()) > 0 || $matchData->getMvp() > 0
            || $matchData->getAgg() > 0) {
            if ($matchData->getCp() > 0) {
                $rec .= 'CP: '.$matchData->getCp().', ';
            }

            if ($matchData->getTd() > 0) {
                $rec .= 'TD: '.$matchData->getTd().', ';
            }

            if ($matchData->getIntcpt() > 0) {
                $rec .= 'INT: '.$matchData->getIntcpt().',';
            }

            if (($matchData->getBh() + $matchData->getSi() + $matchData->getKi()) > 0) {
                $rec .= 'CAS: '.($matchData->getBh() + $matchData->getSi() + $matchData->getKi()).', ';
            }

            if ($matchData->getMvp() > 0) {
                $rec .= 'MVP: '.$matchData->getMvp().', ';
            }

            if ($matchData->getAgg() > 0) {
                $rec .= 'AGG: '.$matchData->getAgg().', ';
            }
        }

        return ['cp'=> $tcp, 'td'=>$ttd,'int'=> $tint,'cas'=> $tcas,'mvp' => $tmvp,'agg'=> $tagg,'rec'=>$rec];
    }

    /**
     * @param Matches $match
     * @param Teams $equipe
     * @return array
     */
    public function listeDesJoueursdUnMatch(Matches $match, Teams $equipe)
    {
        $mDataCollection = $this->doctrineEntityManager->getRepository(MatchData::class)->findBy(
            ['fMatch' => $match->getMatchId()]
        );

        $joueurCollection = [];

        foreach ($mDataCollection as $mData) {
            /** @var MatchData $mdata */
            $joueur = $mData->getFPlayer();
            if ($joueur->getOwnedByTeam() === $equipe) {
                $joueurCollection[] = $joueur;
            }
        }

        return $joueurCollection;
    }

    /**
     * @param Teams $equipe
     * @return int
     */
    public function numeroLibreDelEquipe($equipe)
    {
        $joueurCollection = $this->listeDesJoueursActifsDelEquipe($equipe);

        $numero = 1;

        foreach ($joueurCollection as $joueur) {
            if ($numero == $joueur->getNr()) {
                $numero++;
            } else {
                break;
            }
        }

        return $numero;
    }

    /**
     * @param int $positionId
     * @param int $teamId
     * @return array
     */
    public function ajoutJoueur($positionId, $teamId)
    {
        $position = $this->doctrineEntityManager->getRepository(
            GameDataPlayers::class
        )->findOneBy(['posId' => $positionId]);

        $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $tresors = '';
        $joueur = new Players();

        $count = 0;

        $joueursParPositionCollection = $this->doctrineEntityManager->getRepository(Players::class)->findBy(
            ['fPos' => $position, 'ownedByTeam' => $equipe]
        );

        foreach ($joueursParPositionCollection as $joueurParPosition) {
            if ($joueurParPosition->getStatus()!=7 && $joueurParPosition->getStatus()!=8) {
                $count++;
            }
        }

        if ($equipe && $position) {
            if ($equipe->getTreasury() >= $position->getCost()) {
                if ($count < $position->getQty()) {
                    $tresors = $equipe->getTreasury() - $position->getCost();
                    $equipe->setTreasury($tresors);

                    $joueur->setNr($this->numeroLibreDelEquipe($equipe));

                    $coach = $equipe->getOwnedByCoach();
                    $race = $position->getFRace();

                    if ($coach) {
                        $joueur->setFCid($coach);
                    }

                    if ($race) {
                        $joueur->setFRid($race);
                    }

                    $dateBoughtFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

                    if ($dateBoughtFormat) {
                        $joueur->setDateBought($dateBoughtFormat);
                    }

                    $joueur->setFPos($position);
                    $joueur->setOwnedByTeam($equipe);
                    $joueur->setValue((int)$position->getCost());
                    $joueur->setStatus(1);

                    $this->doctrineEntityManager->persist($joueur);

                    $equipe->setTv($this->equipeService->tvDelEquipe($equipe, $this));

                    $this->doctrineEntityManager->persist($equipe);

                    $this->doctrineEntityManager->flush();

                    return['resultat'=>'ok','joueur'=>$joueur];
                }
                return ['resultat'=>"Plus de place"];
            }
            return ['resultat'=>"Pas assez d'argent"];
        }
        return ['resultat'=>'erreur'];
    }

    /**
     * @param Players $joueur
     * @param EquipeService $equipeService
     * @return array
     */
    public function renvoisOuSuppressionJoueur(Players $joueur, EquipeService $equipeService)
    {
        $equipe = $joueur->getOwnedByTeam();
        $position = $joueur->getFPos();
        $effect="nope";

        if ($equipe  && $position) {
            $matchjoues = $this->doctrineEntityManager->getRepository(MatchData::class)->findBy(['fPlayer'=>$joueur]);
            if (!$matchjoues && $joueur->getType()==1) {
                $effect = "rm";
                $equipe->setTreasury($equipe->getTreasury() + $position->getCost());
                $this->doctrineEntityManager->remove($joueur);
                $this->doctrineEntityManager->persist($joueur);
            } else {
                $this->doctrineEntityManager->persist($equipe);
                $joueur->setStatus(7);
                $dateSoldFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
                if ($dateSoldFormat) {
                    $joueur->setDateSold($dateSoldFormat);
                }
                $this->doctrineEntityManager->persist($joueur);
                $effect = "sld";
            }
            $this->doctrineEntityManager->flush();

            $equipe->setTv($this->equipeService->tvDelEquipe($equipe, $this));

            $this->doctrineEntityManager->persist($equipe);
            $this->doctrineEntityManager->flush();
            $this->doctrineEntityManager->refresh($equipe);

            return [
                'reponse' => $effect,
                'tv' => $equipeService->tvDelEquipe($equipe, $this),
                'tresor' => $equipe->getTreasury(),
                'playercost' => $this->valeurDunJoueur($joueur),
            ];
        }

        return['error'];
    }

    /**
     * @param Players $joueur
     * @return int|mixed|null
     */
    public function valeurDunJoueur(Players $joueur)
    {
        $position = $joueur->getFPos();
        if ($position) {
            $coutCompetencesGagnee = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);
            $coutNiveauSpeciaux = $this->listenivSpeciauxEtSurcout($joueur);
            return $position->getCost() +  $coutCompetencesGagnee['cout'] + $coutNiveauSpeciaux['cout'];
        }
        return 0;
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function listeDesMatchsdUnJoueur(Players $joueur)
    {
        $dataMatchjoues = $this->doctrineEntityManager->getRepository(MatchData::class)->findBy(['fPlayer'=>$joueur]);
        $matchJoue = [];

        foreach ($dataMatchjoues as $dataMatches) {
            $matchJoue[] =  $dataMatches->getFMatch();
        }

        return $matchJoue;
    }

    /**
     * @param Players $joueur
     * @param GameDataSkills $competenceGagnee
     */
    public function ajoutCompetence(Players $joueur, GameDataSkills $competenceGagnee)
    {
        $competenceGagneeParLeJoueur = new PlayersSkills();

        $competenceGagneeParLeJoueur->setFPid($joueur);

        $competenceGagneeParLeJoueur->setFSkill($competenceGagnee);

        if ($competenceGagnee->getCat() == 'C') {
            switch ($competenceGagnee->getSkillId()) {
                case 117:
                    $joueur->setAchMa($joueur->getAchMa()+1);
                    break;
                case 118:
                    $joueur->setAchSt($joueur->getAchSt()+1);
                    break;
                case 119:
                    $joueur->setAchAg($joueur->getAchAg()+1);
                    break;
                case 120:
                    $joueur->setAchAv($joueur->getAchAv()+1);
                    break;
            }
        } else {
            $positionDuJoueur = $joueur->getFPos();

            $normale ='';
            $double = '';

            if ($positionDuJoueur) {
                $double = $positionDuJoueur->getDoub();
                $normale = $positionDuJoueur->getNorm();
            }

            $pos = stripos((string)$normale, (string) $competenceGagnee->getCat());

            if ($pos === false) {
            } else {
                $competenceGagneeParLeJoueur->setType('N');
            }

            $pos = stripos((string)$double, (string)$competenceGagnee->getCat());

            if ($pos === false) {
            } else {
                $competenceGagneeParLeJoueur->setType('D');
            }

            $this->doctrineEntityManager->persist($competenceGagneeParLeJoueur);
            $this->doctrineEntityManager->flush();
        }

        $joueur->setStatus(1);

        $this->doctrineEntityManager->persist($joueur);
        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Teams $equipe
     * @return int
     */
    public function coutTotalJoueurs(Teams $equipe)
    {
        $players = $this->listeDesJoueursDelEquipe($equipe);

        $coutTotalJoueur = 0;

        foreach ($players as $joueur) {
            switch ($joueur->getStatus()) {
                case 7:
                case 8:
                    break;
                default:
                    if ($joueur->getInjRpm() == 0) {
                        $coutTotalJoueur += $this->valeurDunJoueur($joueur);
                    }
                    break;
            }
        }

        return (int)$coutTotalJoueur;
    }

    public function annulerRPMunJoueur(Players $joueur)
    {
        $joueur->setStatus(1);
        $joueur->setInjRpm(0);

        $this->doctrineEntityManager->persist($joueur);

        $this->doctrineEntityManager->flush();
    }

    public function annulerRPMtousLesJoueursDeLequipe($equipe)
    {
        foreach ($this->listeDesJoueursActifsDelEquipe($equipe) as $joueur) {
            if ($joueur->getInjRpm() == 1) {
                $this->annulerRPMunJoueur($joueur);
            }
        }
    }

    public function controleNiveauDuJoueur($equipe)
    {
        foreach ($this->listeDesJoueursActifsDelEquipe($equipe) as $joueur) {
            $xpJoueur = $this->xpDuJoueur($joueur);

            $nbrskill = $this->doctrineEntityManager->getRepository(PlayersSkills::class)->findBy(
                ['fPid' => $joueur->getPlayerId()]
            );

            $nbrskill = count($nbrskill) + $joueur->getAchMa() + $joueur->getAchSt() + $joueur->getAchAv(
            ) + $joueur->getAchAg();

            switch ($nbrskill) {
                case 0:
                    if ($xpJoueur > 5) {
                        $joueur->setStatus(9);
                    }
                    break;
                case 1:
                    if ($xpJoueur > 15) {
                        $joueur->setStatus(9);
                    }
                    break;
                case 2:
                    if ($xpJoueur > 30) {
                        $joueur->setStatus(9);
                    }
                    break;
                case 3:
                    if ($xpJoueur > 50) {
                        $joueur->setStatus(9);
                    }
                    break;
                case 4:
                    if ($xpJoueur > 75) {
                        $joueur->setStatus(9);
                    }
                    break;
                case 5:
                    if ($xpJoueur > 175) {
                        $joueur->setStatus(9);
                    }
                    break;
            }

            $this->doctrineEntityManager->persist($joueur);
        }
    }
}
