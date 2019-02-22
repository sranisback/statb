<?php


namespace App\Service;

use App\Entity\MatchData;

use App\Entity\Teams;

use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;
use Doctrine\ORM\EntityManagerInterface;

class PlayerService
{

    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
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
     * @param Players|null $joueur
     * @return array
     */
    public function statsDuJoueur(Players $joueur = null)
    {
        $coutTotal = 0;

        if ($joueur) {
            $toutesLesCompsDuJoueur = $this->listeDesCompdDeBasedUnJoueur($joueur);
            $compsupp = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);

            $toutesLesCompsDuJoueur .= $compsupp['compgagnee'];

            $statpec = $this->listenivSpeciauxEtSurcout($joueur);

            $toutesLesCompsDuJoueur .= $statpec['nivspec'];

            if ($joueur->getType()!= 1) {
                $toutesLesCompsDuJoueur = '<text class="text-danger">Loner</text>, ';
            }

            $actions = $this->actionsDuJoueur($joueur);

            return ['comp'=>$toutesLesCompsDuJoueur, 'cout'=>$coutTotal, 'actions'=>$actions];
        }
        return [];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function listeDesCompdDeBasedUnJoueur(Players $joueur)
    {
        $positionJoueur  = $joueur->getFPos();

        if ($positionJoueur) {
            $idcompCollection = explode(",", (string) $positionJoueur->getSkills());

            $listeCompDeBase = '';

            foreach ($idcompCollection as $idComp) {
                $comp = $this->doctrineEntityManager->getRepository(GameDataSkills::class)->findOneBy(
                    ['skillId' => $idComp]
                );

                $listeCompDeBase .= '<text class="test-primary">'.$comp->getName().'</text>, ';
            }

            if ($listeCompDeBase == '<text class="test-primary"></text>, ') {
                $listeCompDeBase = '';
            }


            return $listeCompDeBase;
        }

        return 'Error';
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
}
