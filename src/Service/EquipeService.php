<?php

namespace App\Service;

use App\Entity\ClassementGeneral;
use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataStadium;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Entity\Matches;

use App\Enum\AnneeEnum;
use App\Enum\NiveauStadeEnum;
use App\Factory\PlayerFactory;
use App\Factory\TeamsFactory;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gumlet\ImageResize;

class EquipeService
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;
    /**
     * @var SettingsService
     */
    private SettingsService $settingsService;

    private InfosService $infoService;

    /**
     * @var int
     */
    private int $baseElo = 150;

    /**
     * @var int
     */
    private int $coutpop = 10_000;
    /**
     * @var int
     */
    private int $coutAssistant = 10_000;
    /**
     * @var int
     */
    private int $coutCheer = 10_000;
    /**
     * @var int
     */
    private int $coutApo = 50_000;
    /**
     * @var int
     */
    private int $payementStade = 70_000;

    private const MORTS_VIVANTS = 'Morts vivants';
    private const OWA = 'OWA';

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        SettingsService $settingsService,
        InfosService $infoService
    ) {
        $this->infoService = $infoService;
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
     * @return array<string,mixed>
     */
    public function valeurInducementDelEquipe(Teams $equipe): array
    {
        $equipeRace = $equipe->getFRace();

        if ($equipeRace !== null) {
            $inducement['rerolls'] = $equipe->getRerolls() * $equipeRace->getCostRr();
        }

        $inducement['pop'] = ($equipe->getFf() + $equipe->getFfBought()) * 10_000;
        $inducement['asscoaches'] = $equipe->getAssCoaches() * 10_000;
        $inducement['cheerleader'] = $equipe->getCheerleaders() * 10_000;
        $inducement['apo'] = $equipe->getApothecary() * 50_000;
        $inducement['total'] = $inducement['rerolls'] + $inducement['pop']
            + $inducement['asscoaches'] + $inducement['cheerleader'] + $inducement['apo'];

        return $inducement;
    }

    /**
     * @param Teams $equipe
     * @param array<Matches> $matchesCollection
     * @return array<string,int>
     */
    public function resultatsDelEquipe(Teams $equipe, array $matchesCollection): array
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
     * @param array<Matches> $matchesCollection
     * @return array<mixed>
     */
    public function resultatsEtDetailsDeLequipe(Teams $equipe, array $matchesCollection) : array
    {
        return array_merge($this->resultatsDelEquipe($equipe, $matchesCollection), $this->detailsScoreEquipe($equipe));
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @return array<string,int>
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
        } elseif (($equipe === $match->getTeam1() && $match->getTeam1Score() === $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() === $match->getTeam2Score())) {
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
    public function createTeam(string $teamname, int $coachid, int $raceid)
    {
        $race = $this->doctrineEntityManager->getRepository(Races::class)->findOneBy(['raceId' => $raceid]);
        $coach = $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(array('coachId' => $coachid));

        $stade = new Stades();
        $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(['id' => 0]);

        $stade->setFTypeStade($typeStade);
        $stade->setTotalPayement(0);
        $stade->setNom('La prairie verte');
        $stade->setNiveau(0);
        $this->doctrineEntityManager->persist($stade);

        $equipe = TeamsFactory::lancerEquipe(
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

        $this->infoService->equipeEstCree($equipe);

        $equipeId = $equipe->getTeamId();

        if (!empty($equipeId)) {
            return $equipeId;
        }

        return null;
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @param PlayerService $playerService
     * @return array<string,int|null>
     */
    public function ajoutInducement(Teams $equipe, string $type, PlayerService $playerService): array
    {
        $nbr = 0;
        $inducost = 0;

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race !== null) {
                    $coutRR = $race->getCostRr();
                    $nbr = $equipe->getRerolls();

                    if (count($matches) > 0) {
                        $coutRR *= 2;
                    }
                    if ($equipe->getTreasury() >= $coutRR) {
                        $inducost = $coutRR;
                        $nbr += 1;
                        $equipe->setRerolls($nbr);
                    }
                }
                break;
            case "pop":
                $nbr = $equipe->getFfBought() + $equipe->getFf();

                if (count($matches) === 0 && $equipe->getTreasury() >= $this->coutpop) {
                    $nbr += 1;
                    $equipe->setFfBought($equipe->getFfBought() + 1);
                    $inducost = $this->coutpop;
                }
                break;
            case "ac":
                $nbr = $equipe->getAssCoaches();

                if ($equipe->getTreasury() >= $this->coutAssistant) {
                    $nbr += 1;
                    $equipe->setAssCoaches($nbr);
                    $inducost = $this->coutAssistant;
                }
                break;
            case "chl":
                $nbr = $equipe->getCheerleaders();

                if ($equipe->getTreasury() >= $this->coutCheer) {
                    $nbr += 1;
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
                    $nbr += 50_000;
                    $stadeDelEquipe->setTotalPayement($nbr);
                    $this->doctrineEntityManager->persist($stadeDelEquipe);
                    $inducost = 70_000;
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
     * @return array<string,int|null>
     */
    public function supprInducement(Teams $equipe, string $type, PlayerService $playerService): array
    {
        $nbr = 0;
        $inducost = 0;

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                $race = $equipe->getFRace();

                if ($race !== null && $equipe->getRerolls() > 0) {
                    $inducost = $race->getCostRr();
                    $nbr = $equipe->getRerolls() - 1;
                    $equipe->setRerolls($nbr);
                }
                break;
            case "pop":
                $nbr = $equipe->getFfBought() + $equipe->getFf();
                if (count($matches) === 0 && $equipe->getFfBought() > 0) {
                    $inducost = $this->coutpop;
                    $nbr -= 1;
                    $equipe->setFfBought($equipe->getFfBought() - 1);
                }
                break;
            case "ac":
                $nbr = $equipe->getAssCoaches();
                if ($nbr > 0) {
                    $inducost = $this->coutAssistant;
                    $nbr -= 1;
                    $equipe->setAssCoaches($nbr);
                }
                break;
            case "chl":
                $nbr = $equipe->getCheerleaders();
                if ($nbr > 0) {
                    $inducost = $this->coutCheer;
                    $nbr -= 1;
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
                    $nbr -= 50_000;
                    $stadeDelEquipe->setTotalPayement($nbr);
                    $inducost = $this->payementStade;
                }
                break;
        }

        if (count($matches) === 0) {
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
     * @return array<int,mixed>
     */
    public function eloDesEquipes(int $year): array
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
                $r[$equipe1Id] += $nbrDeCoachesActifsDivParDeux * ($resultat1 - $pourcentageVictoireEquipe1);
                $r[$equipe2Id] += $nbrDeCoachesActifsDivParDeux * ($resultat2 - $pourcentageVictoireEquipe2);
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
     * @return mixed[]
     */
    public function listeDesAnciennesEquipes(Coaches $coachActif, int $annee): array
    {
        $anciennesEquipes = [];

        for ($anneeAjoutee = 0; $anneeAjoutee < $annee; $anneeAjoutee++) {
            $retourRequete =
                $this->doctrineEntityManager->getRepository(Teams::class)->toutesLesEquipesDunCoachParAnnee(
                    $coachActif,
                    $anneeAjoutee
                );

            if (!empty($retourRequete)) {
                $anciennesEquipes = array_merge($anciennesEquipes, $retourRequete);
            }
        }

        return $anciennesEquipes;
    }

    /**
     * @param Teams $equipe
     * @return GameDataPlayers
     */
    public function positionDuJournalier(Teams $equipe): ?\App\Entity\GameDataPlayers
    {
        /** @var Races $race */
        $race = $equipe->getFRace();

        switch ($race->getName()) {
            case EquipeService::MORTS_VIVANTS:
                return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)
                    ->findOneBy(['posId' => '171']);
            case EquipeService::OWA:
                return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)->findOneBy(
                    ['fRace' => $equipe->getFRace(), 'qty' => '12']
                );
            default:
                $test = $this->doctrineEntityManager->getRepository(GameDataPlayers::class)->findOneBy(
                    ['fRace' => $equipe->getFRace(), 'qty' => '16']
                );
                return $this->doctrineEntityManager->getRepository(GameDataPlayers::class)->findOneBy(
                    ['fRace' => $equipe->getFRace(), 'qty' => '16']
                );
        }
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return int[]|mixed[]
     */
    public function gestionDesJournaliers(Teams $equipe, PlayerService $playerService): array
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
    public function suppressionDesJournaliers(int $nbrDeJournalierAvendre, Teams $equipe): int
    {
        $nombreVendu = 0;

        foreach ($this->doctrineEntityManager->getRepository(
            Players::class
        )->listeDesJournaliersDeLequipe($equipe) as $journalierAVendre) {
            /** @var Players $journalierAVendre */
            if ($nombreVendu < $nbrDeJournalierAvendre && $journalierAVendre->getStatus() != 9) {
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

        return $nombreVendu;
    }

    /**
     * @param int $nbrDeJournalier
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return int
     */
    public function ajoutDesJournaliers(int $nbrDeJournalier, Teams $equipe, PlayerService $playerService): int
    {
        $nombreAjoute = 0;

        /** @var GameDataPlayers $positionJournalier */
        $positionJournalier = $this->positionDuJournalier($equipe);

        for ($x = 0; $x < $nbrDeJournalier; $x++) {
            /** @var Players $journalier */
            $journalier = PlayerFactory::nouveauJoueur(
                $positionJournalier,
                $playerService->numeroLibreDelEquipe($equipe),
                $equipe,
                true,
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
    public function checkEquipe(Teams $equipe, PlayerService $playerService): void
    {
        $playerService->controleNiveauDesJoueursDelEquipe($equipe);

        $this->gestionDesJournaliers($equipe, $playerService);

        $equipe->setTv($this->tvDelEquipe($equipe, $playerService));

        $this->doctrineEntityManager->persist($equipe);

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Teams $equipe
     * @return array<mixed>
     */
    public function detailsScoreEquipe(Teams $equipe):array
    {
        /** @var ClassementGeneral $detailsPoints */
        $detailsPoints = $this->doctrineEntityManager
            ->getRepository(ClassementGeneral::class)->findOneBy(['equipe' => $equipe->getTeamId()]);

        if (!empty($detailsPoints)) {
            return [
                'bonus' => $detailsPoints->getBonus(),
                'tdMis' => $detailsPoints->getTdPour(),
                'tdPris' => $detailsPoints->getTdContre(),
                'sortiesPour' => $detailsPoints->getCasPour(),
                'sortiesContre' => $detailsPoints->getCasContre(),
                'score' => $detailsPoints->getPoints(),
                'penalite' => $detailsPoints->getPenalite()
            ]
                ;
        }
        return [
            'bonus' => 0,
            'tdMis' => 0,
            'tdPris' => 0,
            'sortiesPour' => 0,
            'sortiesContre' => 0,
            'score' => 0,
            'penalite' => 0
        ]
            ;
    }

    public function compileLesEquipes(Coaches $coachActif)
    {
        $annee = $this->settingsService->anneeCourante();

        $etiquetteAnne = (new AnneeEnum)->numeroToAnnee();

        $compilEquipes = [];

        /** @var Teams $equipe */
        foreach ($this->listeDesAnciennesEquipes($coachActif, $annee) as $equipe) {
            $compilEquipes[] = [
                'equipe' => $equipe,
                'resultats' => $this->resultatsEtDetailsDeLequipe(
                    $equipe,
                    $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe)
                ),
                'annee' => $etiquetteAnne[$equipe->getYear()],
            ];
        }

        return $compilEquipes;
    }

    public function compileEquipesAnneeEnCours(Coaches $coach)
    {
        $annee = $this->settingsService->anneeCourante();

        $equipesEtResultatsDuCoach = [];

        foreach ($this->doctrineEntityManager->getRepository(Teams::class)->toutesLesEquipesDunCoachParAnnee(
            $coach,
            $annee
        ) as $equipe) {
            $equipesEtResultatsDuCoach[] = [
                'equipe' => $equipe,
                'resultats' => $this->resultatsEtDetailsDeLequipe(
                    $equipe,
                    $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe)
                )
            ];
        }

        return $equipesEtResultatsDuCoach;
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return array
     */
    public function feuilleDequipeComplete(Teams $equipe, PlayerService $playerService)
    {
        $pdata = [];

        /** @var Players $players */
        $players = $this->doctrineEntityManager
            ->getRepository(Players::class)
            ->listeDesJoueursPourlEquipe($equipe);

        $pdata = $playerService->ligneJoueur($players);

        $tdata = $this->calculsInducementEquipe($equipe, $playerService);

        return  [
            'players' => $players,
            'team' => $equipe,
            'pdata' => $pdata,
            'tdata' => $tdata,
            'annee' => $this->settingsService->anneeCourante(),
            'niveauStade' => NiveauStadeEnum::numeroVersNiveauDeStade()
        ];
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return mixed
     */
    public function calculsInducementEquipe(Teams $equipe, PlayerService $playerService)
    {
        $inducement = $this->valeurInducementDelEquipe($equipe);

        $tdata['playersCost'] = $playerService->coutTotalJoueurs($equipe);
        $tdata['rerolls'] = $inducement['rerolls'];
        $tdata['pop'] = $inducement['pop'];
        $tdata['asscoaches'] = $inducement['asscoaches'];
        $tdata['cheerleader'] = $inducement['cheerleader'];
        $tdata['apo'] = $inducement['apo'];
        $tdata['tv'] = $this->tvDelEquipe($equipe, $playerService);
        return $tdata;
    }

    /**
     * @param Request $request
     * @param $logoDirectory
     * @param Teams $equipe
     * @throws \Gumlet\ImageResizeException
     */
    public function enregistreLogo(Request $request, $logoDirectory, Teams $equipe)
    {
        $logoUpload = $request->files->all();

        /** @var UploadedFile $logo */
        $logo = $logoUpload['logo_envoi']['logo'];

        $logo->move($logoDirectory, $logo->getClientOriginalName());

        $image = new ImageResize($logoDirectory . '/' . $logo->getClientOriginalName());
        $image->resizeToBestFit(200, 114);
        $image->save($logoDirectory. '/' . $logo->getClientOriginalName());

        $equipe->setLogo($logo->getClientOriginalName());

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        $this->doctrineEntityManager->refresh($equipe);
    }

    /**
     * @param int $teamId
     * @param string $action
     * @param string $type
     * @param PlayerService $playerService
     * @return array
     */
    public function gestionInducement(
        int $teamId,
        string $action,
        string $type,
        PlayerService $playerService
    ): array {
        /** @var Teams $equipe */
        $equipe = $this->doctrineEntityManager
            ->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($action === 'add') {
            $coutEtnbr = $this->ajoutInducement($equipe, $type, $playerService);
        } else {
            $coutEtnbr = $this->supprInducement($equipe, $type, $playerService);
        }
        $tv = $this->tvDelEquipe($equipe, $playerService);

        return [
            "tv" => $tv,
            "ptv" => $tv / 1_000,
            "tresor" => $equipe->getTreasury(),
            "inducost" => $coutEtnbr['inducost'],
            "type" => $type,
            "nbr" => $coutEtnbr['nbr'],
        ];
    }

    /**
     * @param Teams $equipe
     * @param $logoDirectory
     */
    public function supprimerLogo(Teams $equipe, $logoDirectory)
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($logoDirectory . '/' . $equipe->getLogo());

        $equipe->setLogo(null);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        $this->doctrineEntityManager->refresh($equipe);
    }

    /**
     * @param Teams $equipe
     */
    public function mettreEnFranchise(Teams $equipe)
    {
        if ($equipe->getFranchise() == false) {
            $equipe->setFranchise(true);
        } else {
            $equipe->setFranchise(false);
        }

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);
    }
}
