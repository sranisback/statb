<?php


namespace App\Service;

use App\Entity\MatchData;

use Doctrine\Common\Persistence\ManagerRegistry;

use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;

class playerService
{

    private $doctrineEntityManager;

    public function __construct(ManagerRegistry $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param Players
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
     * @param Players $joueur
     * @return array
     */
    public function statsDuJoueur(Players $joueur)
    {

        $coutTotal = 0;

        $toutesLesCompsDuJoueur = $this->listeDesCompdDeBasedUnJoueur($joueur);
        $compsupp = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);

        $toutesLesCompsDuJoueur .= $compsupp[0];

       // $coutTotal += $compsupp[1];

        $statpec = $this->listenivSpeciauxEtSurcout($joueur);

        $toutesLesCompsDuJoueur .= $statpec[0];

       // $coutTotal += $statpec[1];

        $actions = $this->actionsDuJoueur($joueur);

        return [$toutesLesCompsDuJoueur, $coutTotal, $actions];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function listeDesCompdDeBasedUnJoueur(Players $joueur)
    {

        $idcompCollection = explode(",", $joueur->getFPos()->getSkills());

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

        return [$listCompGagnee, $coutTotal];
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

        return [$listSupp, $cout];
    }

    /**
     * @param Players $joueur
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

        return [$tMatch, $tcp, $ttd, $tint, $tcas, $tmvp, $tagg];

    }

    /**
     * @param Players $joueur
     * @return int
     */
    public function xpDuJoueur(Players $joueur)
    {

        $actions = $this->actionsDuJoueur($joueur);

        return $actions[1] + ($actions[2] * 3) + ($actions[3] * 2) + ($actions[4] * 2) + ($actions[5] * 5);

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
}