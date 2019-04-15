<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\Races;
use App\Entity\Setting;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Entity\Matches;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class EquipeService
{

    private $doctrineEntityManager;

    private $tresorDepart = 1000000;

    private $baseElo = 150;

    private $coutpop = 10000;
    private $coutAssistant = 10000;
    private $coutCheer = 10000;
    private $coutApo = 50000;
    private $payementStade = 70000;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param int $annee
     * @return array
     */
    public function toutesLesTeamsParAnnee($annee = 1)
    {
        $equipeCollection  = $this->doctrineEntityManager->getRepository(Teams::class)->toutesLesEquipesActivesDuneAnnee($annee);

        foreach ($equipeCollection as $number => $line){
            $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
                ['teamId' => $line['teamId']]
            );
            $line['tv'] = $this->tvDelEquipe($equipe);
            //var_dump($line);
            $equipeCollection[$number] = $line;
        }

        return $equipeCollection;
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesMatchs(Teams $equipe) :array
    {
        $matches1 = $this->doctrineEntityManager->getRepository(Matches::class)->findBy(
            ['team1' => $equipe->getTeamId()],
            ['dateCreated' => 'DESC']
        );

        $matches2 = $this->doctrineEntityManager->getRepository(Matches::class)->findBy(
            ['team2' => $equipe->getTeamId()],
            ['dateCreated' => 'DESC']
        );

        $matches = array_merge($matches1, $matches2);

        return $matches;
    }

    /**
     * @param Teams $equipe
     * @param array $matchesCollection
     * @return array
     */
    public function resultatsDelEquipe(Teams $equipe, Array $matchesCollection)
    {
        $TotalWin = 0;
        $Totaldraw = 0;
        $Totalloss = 0;

        foreach ($matchesCollection as $match) {
            $results = $this->resultatDuMatch($equipe, $match);

            $TotalWin += $results['win'];
            $Totaldraw += $results['draw'];
            $Totalloss += $results['loss'];
        }

        return ['win'=>$TotalWin,'draw'=> $Totaldraw,'loss'=> $Totalloss];
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @return array
     */
    public function resultatDuMatch(Teams $equipe, Matches $match): array
    {
        $win = 0;
        $loss = 0;
        $draw = 0;

        if (($equipe === $match->getTeam1() && $match->getTeam1Score() > $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() < $match->getTeam2Score())) {
            $win++;
        } elseif (($equipe === $match->getTeam1() && $match->getTeam1Score() < $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() > $match->getTeam2Score())) {
            $loss++;
        } elseif (($equipe === $match->getTeam1() && $match->getTeam1Score() == $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() == $match->getTeam2Score())) {
            $draw++;
        }

        return ['win'=>$win, 'loss'=>$loss,'draw'=> $draw];
    }

    /**
     * @param string $teamname
     * @param int $coachid
     * @param int $raceid
     * @return int|null
     */
    public function createTeam($teamname, $coachid, $raceid)
    {
        $setting = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);
        $race = $this->doctrineEntityManager->getRepository(Races::class)->findOneBy(['raceId' => $raceid]);
        $coach = $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(array('coachId' => $coachid));

        $currentYear = 0;
        $teamid = 0;
        $team = new Teams();

        $team->setTreasury($this->tresorDepart);
        $team->setName($teamname);
        $team->setElo($this->baseElo);
        $team->setTv(0);

        $stade = new Stades();
        $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=>0]);

        $stade->setFTypeStade($typeStade);
        $stade->setTotalPayement(0);
        $this->doctrineEntityManager->persist($stade);
        $team->setFStades($stade);

        if ($setting) {
            try {
                $currentYear = $setting->getValue();
            } catch (ORMException $e) {
            }

            $team->setYear((int)$currentYear);
        }
        if ($race) {
            $team->setFRace($race);
        }
        if ($coach) {
            $team->setOwnedByCoach($coach);
        }

        try {
            $this->doctrineEntityManager->persist($team);
            $this->doctrineEntityManager->flush();
            $this->doctrineEntityManager->refresh($team);
            $teamid = $team->getTeamId();
        } catch (ORMException $e) {
        }

        return $teamid;
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function valeurInducementDelEquipe(Teams $equipe)
    {
        $equipeRace = $equipe->getFRace();

        if ($equipeRace) {
            $inducement['rerolls'] = $equipe->getRerolls() * $equipeRace->getCostRr();
        }

        $inducement['pop'] = ($equipe->getFf() + $equipe->getFfBought()) * 10000;
        $inducement['asscoaches'] = $equipe->getAssCoaches() * 10000;
        $inducement['cheerleader'] = $equipe->getCheerleaders() * 10000;
        $inducement['apo'] = $equipe->getApothecary() * 50000;
        $inducement['total']= $inducement['rerolls'] + $inducement['pop']
            + $inducement['asscoaches'] + $inducement['cheerleader'] + $inducement['apo'];

        return $inducement;
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return int
     */
    public function tvDelEquipe(Teams $equipe, PlayerService $playerService)
    {
        $coutTotalJoueur = $playerService->coutTotalJoueurs($equipe);

        $inducement = $this->valeurInducementDelEquipe($equipe);

        return $coutTotalJoueur + $inducement['total'];
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @return array
     */
    public function ajoutInducement(Teams $equipe, $type, PlayerService $playerService)
    {
        $nbr = 0;
        $inducost = 0;

        $nbrmatch = $this->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race) {
                    $coutRR = $race->getCostRr();
                    $nbr = $equipe->getRerolls();

                    if (count($nbrmatch) > 0) {
                        $coutRR = $coutRR * 2;
                    }
                    if ($equipe->getTreasury() >= $coutRR) {
                        $inducost = $coutRR;
                        $nbr = $nbr + 1;
                        $equipe->setRerolls($nbr);
                    }
                }
                break;
            case "pop":
                    $nbr = $equipe->getFfBought()+$equipe->getFf();

                if (count($nbrmatch) == 0) {
                    if ($equipe->getTreasury() >= $this->coutpop) {
                        $nbr = $nbr + 1;
                        $equipe->setFfBought($equipe->getFfBought()+1);
                        $inducost = $this->coutpop;
                    }
                }
                break;
            case "ac":
                $nbr = $equipe->getAssCoaches();

                if ($equipe->getTreasury() >= $this->coutAssistant) {
                    $nbr = $nbr + 1;
                    $equipe->setAssCoaches($nbr);
                    $inducost = $this->coutAssistant;
                }
                break;
            case "chl":
                $nbr = $equipe->getCheerleaders();

                if ($equipe->getTreasury() >= $this->coutCheer) {
                    $nbr = $nbr + 1;
                    $equipe->setCheerleaders($nbr);
                    $inducost = $this->coutCheer;
                }
                break;
            case "apo":
                $equipe->getApothecary() == true ? $nbr = 1 : $nbr = 0;
                if ($equipe->getTreasury() >= $this->coutApo && $equipe->getApothecary() == false) {
                    $nbr = 1;
                    $equipe->setApothecary(1);
                    $inducost = $this->coutApo;
                }
                break;
            case "pay":
                $stadeDelEquipe = $equipe->getFStades();

                if ($stadeDelEquipe->getId() == 0) {
                    $stadeDelEquipe = new Stades();
                    $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(
                        ['id' => 0]
                    );

                    $stadeDelEquipe->setNom('La prairie verte ');
                    $stadeDelEquipe->setFTypeStade($typeStade);
                    $stadeDelEquipe->setTotalPayement(0);
                    $this->doctrineEntityManager->persist($stadeDelEquipe);
                    $equipe->setFStades($stadeDelEquipe);
                }

                $nbr = $stadeDelEquipe->getTotalPayement();

                if ($equipe->getTreasury() >= $this->payementStade) {
                    $nbr += 50000;
                    $stadeDelEquipe->setTotalPayement($nbr);
                    $inducost = 70000;
                }
        }

        $nouveauTresor = $equipe->getTreasury() - $inducost;
        $equipe->setTreasury($nouveauTresor);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $this->tvDelEquipe($equipe, $playerService);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        return ['inducost'=>$inducost, 'nbr'=>$nbr];
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @return array
     */
    public function supprInducement(Teams $equipe, $type, PlayerService $playerService)
    {
        $nbr = 0;
        $inducost = 0;

        $nbrmatch = $this->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race) {
                    if ($equipe->getRerolls()>0) {
                        $inducost = $race->getCostRr();
                        $nbr = $equipe->getRerolls()-1;
                        $equipe->setRerolls($nbr);
                    }
                }
                break;
            case "pop":
                $nbr = $equipe->getFfBought()+$equipe->getFf();
                if (count($nbrmatch) == 0) {
                    if ($equipe->getFfBought() > 0) {
                        $inducost = $this->coutpop;
                        $nbr = $nbr - 1;
                        $equipe->setFfBought($equipe->getFfBought()-1);
                    }
                }
                break;
            case "ac":
                $nbr = $equipe->getAssCoaches();
                if ($nbr > 0) {
                    $inducost = $this->coutAssistant;
                    $nbr = $nbr - 1;
                    $equipe->setAssCoaches($nbr);
                }
                break;
            case "chl":
                $nbr = $equipe->getCheerleaders();
                if ($nbr > 0) {
                    $inducost = $this->coutCheer;
                    $nbr = $nbr - 1;
                    $equipe->setCheerleaders($nbr);
                }
                break;
            case "apo":
                $equipe->getApothecary() == true ? $nbr = 1 : $nbr = 0;
                if ($equipe->getApothecary() == true) {
                    $inducost = $this->coutApo;
                    $nbr = 0;
                    $equipe->setApothecary(0);
                }
                break;
            case "pay":
                $stadeDelEquipe = $equipe->getFStades();
                $nbr = $stadeDelEquipe->getTotalPayement();
                if ($nbr > 0) {
                    $nbr = $nbr - 50000;
                    $stadeDelEquipe->setTotalPayement($nbr);
                    $inducost = $this->payementStade;
                }
                break;
        }

        if (count($nbrmatch)==0) {
            $nouveauTresor = $equipe->getTreasury() + $inducost;
            $equipe->setTreasury($nouveauTresor);
        }

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $this->tvDelEquipe($equipe, $playerService);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        return ['inducost'=>$inducost, 'nbr'=>$nbr];
    }

    public function classementGeneral()
    {
        $setting = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        return $this->doctrineEntityManager->getRepository(Teams::class)->classement( $setting->getValue(),0);
    }

    /**
     * @return mixed
     */
    public function eloDesEquipes($year)
    {
        $equipeCollection = $this->doctrineEntityManager->getRepository(Teams::class)->findBy(['year' => $year]);

        $nbrDeCoachesActifsDivParDeux = $this->doctrineEntityManager->getRepository(
            Teams::class
        )->nbrCoachAyantUneEquipelAnneeEnCours(3);

        $r = [];

        foreach ($equipeCollection as $equipe) {
            $r[$equipe->getTeamId()] = 150;
        }

        $matchDelAnneeEnCour = $this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnne(3);

        /** @var Matches $match */
        foreach ($matchDelAnneeEnCour as $match) {
            if ($match->getTeam1Score() > $match->getTeam2Score()) {
                $resultat1 = 1;
                $resultat2 = 0;
            } elseif ($match->getTeam1Score() > $match->getTeam2Score()) {
                $resultat1 = 0;
                $resultat2 = 1;
            } else {
                $resultat1 = 0.5;
                $resultat2 = 0.5;
            }

            $d = 231;
            $equipe1 = $match->getTeam1();
            $equipe2 = $match->getTeam2();

            $pourcentageVictoireEquipe1 = 1 / ( pow(
                10,
                ($r[$equipe2->getTeamId()] - $r[$equipe1->getTeamId()]) / $d
            ) + 1);
            $pourcentageVictoireEquipe2 = 1 / ( pow(
                10,
                ($r[$equipe1->getTeamId()] - $r[$equipe2->getTeamId()]) / $d
            ) + 1);
            $r[$equipe1->getTeamId()] = $r[$equipe1->getTeamId()]
                + ($nbrDeCoachesActifsDivParDeux * ($resultat1 - $pourcentageVictoireEquipe1));
            $r[$equipe2->getTeamId()] = $r[$equipe2->getTeamId()]
                + ($nbrDeCoachesActifsDivParDeux * ($resultat2 - $pourcentageVictoireEquipe2));
        }

        foreach ($equipeCollection as $equipe) {
            if ($r[$equipe->getTeamId()] != null) {
                $equipe->setElo(round($r[$equipe->getTeamId()],2));
            } else {
                $equipe->setElo(150);
            }
        }

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        return $r;
    }
}
