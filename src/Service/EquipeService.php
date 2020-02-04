<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataStadium;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Setting;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Entity\Matches;

use App\Factory\PlayerFactory;
use App\Factory\TeamsFactory;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class EquipeService
{

    private $doctrineEntityManager;
    private $settingsService;

    private $baseElo = 150;

    private $coutpop = 10000;
    private $coutAssistant = 10000;
    private $coutCheer = 10000;
    private $coutApo = 50000;
    private $payementStade = 70000;

    private const MORTS_VIVANTS = 'Morts vivants';

    public function __construct(EntityManagerInterface $doctrineEntityManager, SettingsService $settingsService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->settingsService = $settingsService;
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
        $inducement['total'] = $inducement['rerolls'] + $inducement['pop']
            + $inducement['asscoaches'] + $inducement['cheerleader'] + $inducement['apo'];

        return $inducement;
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

        return ['win' => $TotalWin, 'draw' => $Totaldraw, 'loss' => $Totalloss];
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

        return ['win' => $win, 'loss' => $loss, 'draw' => $draw];
    }

    /**
     * @param string $teamname
     * @param int $coachid
     * @param int $raceid
     * @return int|null
     */
    public function createTeam($teamname, $coachid, $raceid)
    {
        $race = $this->doctrineEntityManager->getRepository(Races::class)->findOneBy(['raceId' => $raceid]);
        $coach = $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(array('coachId' => $coachid));

        $stade = new Stades();
        $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(['id' => 0]);

        $stade->setFTypeStade($typeStade);
        $stade->setTotalPayement(0);
        $stade->setNom('La prairie verte');
        $this->doctrineEntityManager->persist($stade);

        $equipe = (new TeamsFactory)->lancerEquipe(
            $this->settingsService->recupererTresorDepart(),
            $teamname,
            $this->baseElo,
            $stade,
            $this->settingsService->anneeCourante(),
            $race,
            $coach
        );

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);
        $equipeId = $equipe->getTeamId();

        if (!empty($equipeId)) {
            return $equipeId;
        }

        return 0;
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @param PlayerService $playerService
     * @return array
     */
    public function ajoutInducement(Teams $equipe, $type, PlayerService $playerService)
    {
        $nbr = 0;
        $inducost = 0;

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race) {
                    $coutRR = $race->getCostRr();
                    $nbr = $equipe->getRerolls();

                    if (count($matches) > 0) {
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
                $nbr = $equipe->getFfBought() + $equipe->getFf();

                if (count($matches) == 0) {
                    if ($equipe->getTreasury() >= $this->coutpop) {
                        $nbr = $nbr + 1;
                        $equipe->setFfBought($equipe->getFfBought() + 1);
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

                $nbr = $stadeDelEquipe->getTotalPayement();

                if ($equipe->getTreasury() >= $this->payementStade) {
                    $nbr += 50000;
                    $stadeDelEquipe->setTotalPayement($nbr);
                    $this->doctrineEntityManager->persist($stadeDelEquipe);
                    $inducost = 70000;
                }
        }

        $nouveauTresor = $equipe->getTreasury() - $inducost;
        $equipe->setTreasury($nouveauTresor);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $equipe->setTv($this->tvDelEquipe($equipe, $playerService));

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        return ['inducost' => $inducost, 'nbr' => $nbr];
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @param PlayerService $playerService
     * @return array
     */
    public function supprInducement(Teams $equipe, $type, PlayerService $playerService)
    {
        $nbr = 0;
        $inducost = 0;

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race) {
                    if ($equipe->getRerolls() > 0) {
                        $inducost = $race->getCostRr();
                        $nbr = $equipe->getRerolls() - 1;
                        $equipe->setRerolls($nbr);
                    }
                }
                break;
            case "pop":
                $nbr = $equipe->getFfBought() + $equipe->getFf();
                if (count($matches) == 0) {
                    if ($equipe->getFfBought() > 0) {
                        $inducost = $this->coutpop;
                        $nbr = $nbr - 1;
                        $equipe->setFfBought($equipe->getFfBought() - 1);
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

        if (count($matches) == 0) {
            $nouveauTresor = $equipe->getTreasury() + $inducost;
            $equipe->setTreasury($nouveauTresor);
        }

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $equipe->setTv($this->tvDelEquipe($equipe, $playerService));

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        return ['inducost' => $inducost, 'nbr' => $nbr];
    }

    /**
     * @param integer $year
     * @return array
     */
    public function eloDesEquipes($year)
    {
        $equipeCollection = $this->doctrineEntityManager->getRepository(Teams::class)->findBy(['year' => $year]);

        $nbrDeCoachesActifsDivParDeux = $this->doctrineEntityManager->getRepository(
            Teams::class
        )->nbrCoachAyantUneEquipelAnneeEnCours($year);

        $r = [];

        foreach ($equipeCollection as $equipe) {
            $r[$equipe->getTeamId()] = 150;
        }

        $matchDelAnneeEnCour = $this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnne($year);

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

            if (!empty($equipe1) && !empty($equipe2)) {
                $equipe1Id = $equipe1->getTeamId();
                $equipe2Id = $equipe2->getTeamId();

                $pourcentageVictoireEquipe1 = 1 / (pow(
                    10,
                    ($r[$equipe2Id] - $r[$equipe1Id]) / $d
                ) + 1);
                $pourcentageVictoireEquipe2 = 1 / (pow(
                    10,
                    ($r[$equipe1Id] - $r[$equipe2Id]) / $d
                ) + 1);
                $r[$equipe1Id] = $r[$equipe1Id]
                    + ($nbrDeCoachesActifsDivParDeux * ($resultat1 - $pourcentageVictoireEquipe1));
                $r[$equipe2Id] = $r[$equipe2Id]
                    + ($nbrDeCoachesActifsDivParDeux * ($resultat2 - $pourcentageVictoireEquipe2));
            }
        }

        foreach ($equipeCollection as $equipe) {
            if ($r[$equipe->getTeamId()] != null) {
                $equipe->setElo(round($r[$equipe->getTeamId()], 2));
            } else {
                $equipe->setElo(150);
            }
        }

        if (!empty($equipe)) {
            $this->doctrineEntityManager->persist($equipe);
            $this->doctrineEntityManager->flush();
            $this->doctrineEntityManager->refresh($equipe);
        }

        return $r;
    }

    /**
     * @param int $coachActif
     * @param int $annee
     * @return array|null
     */
    public function listeDesAnciennesEquipes($coachActif, $annee)
    {
        $anciennesEquipes = [];

        for ($anneeAjoutee = 0; $anneeAjoutee < $annee; $anneeAjoutee++) {
            $retourRequete =
                $this->doctrineEntityManager->getRepository(Teams::class)->toutesLesEquipesDunCoachParAnnee(
                    $coachActif,
                    $anneeAjoutee
                );

            if (!empty($retourRequete)) {
                foreach ($retourRequete as $equipe) {
                    $anciennesEquipes[] = $equipe;
                }
            }
        }

        return $anciennesEquipes;
    }

    /**
     * @param Teams $equipe
     * @return GameDataPlayers
     */
    public function positionDuJournalier(Teams $equipe)
    {
        /** @var Races $race */
        $race = $equipe->getFRace();

        if ($race->getName() == EquipeService::MORTS_VIVANTS) {
            return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)
                ->findOneBy(['posId' => '171']);
        } else {
            return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)->findOneBy(
                ['fRace' => $equipe->getFRace(), 'qty' => '16']
            );
        }
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     */
    public function gestionDesJournaliers(Teams $equipe, PlayerService $playerService)
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
    public function suppressionDesJournaliers(int $nbrDeJournalierAvendre, Teams $equipe)
    {
        $nombreVendu = 0;

        foreach ($this->doctrineEntityManager->getRepository(
            Players::class
        )->listeDesJournaliersDeLequipe($equipe) as $journalierAVendre) {
            if ($nombreVendu < $nbrDeJournalierAvendre) {
                /** @var Players $journalierAVendre */
                if ($journalierAVendre->getStatus() != 9) {
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
        }

        return $nombreVendu;
    }

    /**
     * @param int $nbrDeJournalier
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return mixed
     */
    public function ajoutDesJournaliers(int $nbrDeJournalier, Teams $equipe, PlayerService $playerService)
    {
        $nombreAjoute = 0;

        /** @var GameDataPlayers $positionJournalier */
        $positionJournalier = $this->positionDuJournalier($equipe);

        for ($x = 0; $x < $nbrDeJournalier; $x++) {
            /** @var Players $journalier */
            $journalier = (new PlayerFactory)->nouveauJoueur(
                $positionJournalier,
                $playerService->numeroLibreDelEquipe($equipe),
                $equipe,
                2,
                null,
                $this->doctrineEntityManager
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
     * @param PlayerService $playerService
     */
    public function checkEquipe(Teams $equipe, PlayerService $playerService)
    {
        $playerService->controleNiveauDesJoueursDelEquipe($equipe);

        $this->gestionDesJournaliers($equipe, $playerService);

        $equipe->setTv($this->tvDelEquipe($equipe, $playerService));

        $this->doctrineEntityManager->persist($equipe);

        $this->doctrineEntityManager->flush();
    }
}
