<?php


namespace App\Service;

use App\Entity\GameDataPlayers;
use App\Entity\MatchData;

use App\Entity\Matches;
use App\Entity\Teams;

use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;
use App\Factory\PlayerFactory;
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

    public function remplirMatchDataDeLigneAzero(Teams $equipe, Matches $match)
    {

        foreach ($this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        ) as $joueur) {
            $this->matchDataService->creationLigneVideDonneeMatch($joueur, $match);
        }
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function statsDuJoueur(Players $joueur)
    {
        $toutesLesCompsDuJoueur = $this->toutesLesCompsdUnJoueur($joueur);

        $actions = $this->actionsDuJoueur($joueur);

        $toutesLesCompsDuJoueur = substr($toutesLesCompsDuJoueur, 0, strlen($toutesLesCompsDuJoueur) - 2);

        return ['comp' => $toutesLesCompsDuJoueur, 'actions' => $actions];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function toutesLesCompsdUnJoueur(Players $joueur)
    {
        $toutesLesCompsDuJoueur = $this->listeDesCompdDeBasedUnJoueur($joueur);
        $compsupp = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);

        $toutesLesCompsDuJoueur .= $compsupp['compgagnee'];

        $statpec = $this->listenivSpeciauxEtSurcout($joueur);

        $toutesLesCompsDuJoueur .= $statpec['nivspec'];

        if ($joueur->getType() != 1) {
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
        $position = $joueur->getFPos();

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
            $idcompCollection = explode(",", (string)$position->getSkills());
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

        return ['compgagnee' => $listCompGagnee, 'cout' => $coutTotal];
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

        return ['nivspec' => $listSupp, 'cout' => $cout];
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

        return [
            'NbrMatch' => $tMatch,
            'cp' => $tcp,
            'td' => $ttd,
            'int' => $tint,
            'cas' => $tcas,
            'mvp' => $tmvp,
            'agg' => $tagg,
        ];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function statutDuJoueur(Players $joueur) //TODO a remplacer par une enum
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
     * @param Matches $match
     * @param Players $joueur
     * @return string
     */
    public function actionDuJoueurDansUnMatch(Matches $match, Players $joueur)
    {
        $actions = '';

        foreach ($this->doctrineEntityManager->getRepository(MatchData::class)->findBy(
            ['fPlayer' => $joueur->getPlayerId(), 'fMatch' => $match]
        ) as $matchData) {
            $actions .=  $this->matchDataService->lectureLignedUnMatch($matchData);
        }

        return $actions;
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

        $count = 0;

        $joueursParPositionCollection = $this->doctrineEntityManager->getRepository(Players::class)->findBy(
            ['fPos' => $position, 'ownedByTeam' => $equipe]
        );

        foreach ($joueursParPositionCollection as $joueurParPosition) {
            if ($joueurParPosition->getStatus() != 7 && $joueurParPosition->getStatus() != 8) {
                $count++;
            }
        }

        if ($equipe && $position) {
            if ($equipe->getTreasury() >= $position->getCost()) {
                if ($count < $position->getQty()) {
                    $tresors = $equipe->getTreasury() - $position->getCost();
                    $equipe->setTreasury($tresors);

                    $joueur = (new PlayerFactory)->nouveauJoueur(
                        $position,
                        $this->numeroLibreDelEquipe($equipe),
                        $equipe,
                        1
                    );

                    $this->doctrineEntityManager->persist($joueur);

                    $equipe->setTv($this->equipeService->tvDelEquipe($equipe, $this));

                    $this->doctrineEntityManager->persist($equipe);

                    $this->doctrineEntityManager->flush();

                    return ['resultat' => 'ok', 'joueur' => $joueur];
                }

                return ['resultat' => "Plus de place"];
            }

            return ['resultat' => "Pas assez d'argent"];
        }

        return ['resultat' => 'erreur'];
    }

    /**
     * @param Teams $equipe
     * @return int
     */
    public function numeroLibreDelEquipe(Teams $equipe)
    {
        $joueurCollection = $this->doctrineEntityManager->getRepository(
            Players::class
        )->listeDesJoueursActifsPourlEquipe($equipe);

        $numero = 1;

        foreach ($joueurCollection as $joueur) {
            if ($numero == $joueur->getNr()) {
                $numero++;
            } else {
                continue;
            }
        }

        return $numero;
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function renvoisOuSuppressionJoueur(Players $joueur)
    {
        $equipe = $joueur->getOwnedByTeam();
        $position = $joueur->getFPos();
        $effect = "nope";

        if ($equipe && $position) {
            $matchjoues = $this->doctrineEntityManager->getRepository(MatchData::class)->listeDesMatchsdUnJoueur(
                $joueur
            );
            if (count($matchjoues)<1 && $joueur->getType() == 1) {
                $effect = "rm";
                $equipe->setTreasury($equipe->getTreasury() + $position->getCost());
                $this->doctrineEntityManager->remove($joueur);
                $this->doctrineEntityManager->flush();
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
                'tv' => $this->equipeService->tvDelEquipe($equipe, $this),
                'tresor' => $equipe->getTreasury(),
                'playercost' => $this->valeurDunJoueur($joueur),
            ];
        }

        return ['error'];
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

            return $position->getCost() + $coutCompetencesGagnee['cout'] + $coutNiveauSpeciaux['cout'];
        }

        return 0;
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
                    $joueur->setAchMa($joueur->getAchMa() + 1);
                    break;
                case 118:
                    $joueur->setAchSt($joueur->getAchSt() + 1);
                    break;
                case 119:
                    $joueur->setAchAg($joueur->getAchAg() + 1);
                    break;
                case 120:
                    $joueur->setAchAv($joueur->getAchAv() + 1);
                    break;
            }
        } else {
            $positionDuJoueur = $joueur->getFPos();

            $normale = '';
            $double = '';

            if ($positionDuJoueur) {
                $double = $positionDuJoueur->getDoub();
                $normale = $positionDuJoueur->getNorm();
            }

            $pos = stripos((string)$normale, (string)$competenceGagnee->getCat());

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
        $players = $this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursPourlEquipe($equipe);

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

    public function annulerRPMtousLesJoueursDeLequipe($equipe)
    {
        foreach ($this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        ) as $joueur) {
            if ($joueur->getInjRpm() == 1) {
                $this->annulerRPMunJoueur($joueur);

                $this->doctrineEntityManager->persist($joueur);
            }
        }

        $this->doctrineEntityManager->flush();
    }

    public function annulerRPMunJoueur(Players $joueur)
    {
        $joueur->setStatus(1);
        $joueur->setInjRpm(0);

        $this->doctrineEntityManager->persist($joueur);

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Teams $equipe
     */
    public function controleNiveauDesJoueursDelEquipe(Teams $equipe)
    {
        $tableComp = [
            0 => 5,
            1 => 15,
            2 => 30,
            3 => 50,
            4 => 75
        ];

        foreach ($this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        ) as $joueur) {
            /** @var Players $joueur */
            $spp = $this->xpDuJoueur($joueur);

            $nbrSkill = $this->nbrCompetencesEtAugmentationsGagnee($joueur);

            if ($spp > $tableComp[$nbrSkill]) {
                $joueur->setStatus(9);
            }

            $this->doctrineEntityManager->persist($joueur);
        }
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
     * @param Matches $match
     * @param Teams $equipe
     * @return string
     */
    public function toutesLesActionsDeLequipeDansUnMatch(Matches $match, Teams $equipe)
    {
        $textAction = '<ul>';

        foreach ($this->doctrineEntityManager->getRepository(MatchData::class)->listeDesJoueursdUnMatch(
            $match,
            $equipe
        ) as $listeActionsDunJoueur) {
            $actions = $this->actionDuJoueurDansUnMatch($match, $listeActionsDunJoueur->getFPlayer());

            /** @var MatchData $listeActionsDunJoueur */
            $name = $listeActionsDunJoueur->getFPlayer()->getName();

            if ($actions != '') {
                if (strlen($name) < 2) {
                    $name = 'Inconnu';
                }

                $actions = $this->actionDuJoueurDansUnMatch($match, $listeActionsDunJoueur->getFPlayer());

                $textAction .= '<li>'.$name.', '.$listeActionsDunJoueur->getFPlayer()->getFPos()->getPos(
                ).'('.$listeActionsDunJoueur->getFPlayer()->getNr().'): '.substr(
                    $actions,
                    0,
                    strlen($actions) - 2
                ).'</li>';
            }
        }

        return $textAction.'</ul>';
    }

    /**
     * @param Players $joueur
     * @return int
     */
    public function nbrCompetencesEtAugmentationsGagnee(Players $joueur)
    {
        $skills = $this->doctrineEntityManager->getRepository(PlayersSkills::class)->findBy(
            ['fPid' => $joueur->getPlayerId()]
        );

        return count($skills) + $joueur->getAchAg() + $joueur->getAchAv()
            + $joueur->getAchMa() + $joueur->getAchSt();
    }
}
