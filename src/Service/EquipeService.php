<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\Setting;
use App\Entity\Teams;
use App\Entity\Matches;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class EquipeService
{

    private $doctrineEntityManager;

    private $tresorDepart = 1000000;

    private $baseElo = 150;

    private $playerService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, PlayerService $playerService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->playerService = $playerService;
    }

    /**
     * @param int $annee
     * @return array
     */
    public function toutesLesTeamsParAnnee($annee = 1)
    {
        if (!empty($this->doctrineEntityManager)) {
            return $this->doctrineEntityManager->getRepository(Teams::class)->findBy(
                ['year' => $annee, 'retired' => false],
                ['name' => 'ASC']
            );
        }

        return [];
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesMatchs(Teams $equipe)
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
     * @return int
     */
    public function coutTotalJoueurs(Teams $equipe)
    {
        $players = $this->playerService ->listeDesJoueursDelEquipe($equipe);

        $coutTotalJoueur = 0;

        foreach ($players as $joueur) {
            switch ($joueur->getStatus()) {
                case 7:
                case 8:
                    break;
                default:
                    if ($joueur->getInjRpm() == 0) {
                        $coutTotalJoueur += $this->playerService->valeurDunJoueur($joueur);
                    }
                    break;
            }
        }

        return (int)$coutTotalJoueur;
    }

    /**
     * @param Teams $equipe
     * @return int
     */
    public function tvDelEquipe(Teams $equipe)
    {
        $coutTotalJoueur = $this->coutTotalJoueurs($equipe);

        $inducement = $this->valeurInducementDelEquipe($equipe);

        return $coutTotalJoueur + $inducement['total'];
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @return array
     */
    public function ajoutInducement(Teams $equipe, $type)
    {
        $nbr = 0;
        $inducost = 0;
        $coutpop = 10000;
        $coutAssistant = 10000;
        $coutCheer = 10000;
        $coutApo = 50000;

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
                    if ($equipe->getTreasury() >= $coutpop) {
                        $nbr = $nbr + 1;
                        $equipe->setFfBought($equipe->getFfBought()+1);
                        $inducost = $coutpop;
                    }
                }
                break;
            case "ac":
                $nbr = $equipe->getAssCoaches();

                if ($equipe->getTreasury() >= $coutAssistant) {
                    $nbr = $nbr + 1;
                    $equipe->setAssCoaches($nbr);
                    $inducost = $coutAssistant;
                }
                break;
            case "chl":
                $nbr = $equipe->getCheerleaders();

                if ($equipe->getTreasury() >= $coutCheer) {
                    $nbr = $nbr + 1;
                    $equipe->setCheerleaders($nbr);
                    $inducost = $coutCheer;
                }
                break;
            case "apo":
                $equipe->getApothecary() == true ? $nbr = 1 : $nbr = 0;
                if ($equipe->getTreasury() >= $coutApo && $equipe->getApothecary() == false) {
                    $nbr = 1;
                    $equipe->setApothecary(1);
                    $inducost = $coutApo;
                }
                break;
        }

        $nouveauTresor = $equipe->getTreasury() - $inducost;
        $equipe->setTreasury($nouveauTresor);
        $equipe->setTv($this->tvDelEquipe($equipe));

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
    public function supprInducement(Teams $equipe, $type)
    {
        $nbr = 0;
        $inducost = 0;
        $coutpop = 10000;
        $coutAssistant = 10000;
        $coutCheer = 10000;
        $coutApo = 50000;

        $nbrmatch = $this->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race) {
                    $inducost = $race->getCostRr();

                    if ($equipe->getRerolls()>0) {
                        $nbr = $equipe->getRerolls()-1;
                        $equipe->setRerolls($nbr);
                    }
                }
                break;
            case "pop":
                $nbr = $equipe->getFfBought()+$equipe->getFf();

                if (count($nbrmatch) == 0) {
                    $inducost = $coutpop;

                    if ($equipe->getFfBought() > 0) {
                        $nbr = $nbr - 1;
                        $equipe->setFfBought($equipe->getFfBought()-1);
                    }
                }
                break;
            case "ac":
                $nbr = $equipe->getAssCoaches();
                $inducost = $coutAssistant;

                if ($nbr > 0) {
                    $nbr = $nbr - 1;
                    $equipe->setAssCoaches($nbr);
                }
                break;
            case "chl":
                $nbr = $equipe->getCheerleaders();
                $inducost = $coutCheer;

                if ($nbr > 0) {
                    $nbr = $nbr - 1;
                    $equipe->setCheerleaders($nbr);
                }
                break;
            case "apo":
                $equipe->getApothecary() == true ? $nbr = 1 : $nbr = 0;
                $inducost = $coutApo;
                if ($equipe->getApothecary() == true) {
                    $nbr = 0;
                    $equipe->setApothecary(0);
                }
                break;
        }

        if (count($nbrmatch)==0 && $nbr>0) {
            $nouveauTresor = $equipe->getTreasury() + $inducost;
            $equipe->setTreasury($nouveauTresor);
        }

        $equipe->setTv($this->tvDelEquipe($equipe));

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        return ['inducost'=>$inducost, 'nbr'=>$nbr];
    }

    public function classementGeneral()
    {
        $setting = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);
        $classGen = $this->doctrineEntityManager->getRepository(Teams::class)->classement($setting->getValue(),0);

        foreach ($classGen as $line) {
            $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $line['team_id']]);
            $line['tv'] = $this->tvDelEquipe($equipe);
        }
        return $classGen;
    }

}
