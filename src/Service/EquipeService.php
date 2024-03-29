<?php

namespace App\Service;

use App\Entity\ClassementGeneral;
use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\ScoreCalcul;
use App\Entity\SpecialRule;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\NiveauStadeEnum;
use App\Enum\RulesetEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Nette\Utils\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @var InducementService
     */
    private InducementService $inducementService;

    /**
     * @var EquipeGestionService
     */
    private EquipeGestionService $equipeGestionService;

    public $raceChoice = ['Elus du chaos', 'Nains du chaos', 'Nordiques', 'Renégats du chaos'];

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        SettingsService $settingsService,
        InducementService $inducementService,
        EquipeGestionService $equipeGestionService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->settingsService = $settingsService;
        $this->inducementService = $inducementService;
        $this->equipeGestionService = $equipeGestionService;
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
     * @return array
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
            } elseif ($match->getTeam1Score() < $match->getTeam2Score()) {
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
     * @param Coaches $coachActif
     * @param int $annee
     * @return array
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
     * @param Teams $equipe
     * @return array
     */
    public function detailsScoreEquipe(Teams $equipe) : array
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

    /**
     * @param Coaches|null $coachActif
     * @return array
     */
    public function compileLesEquipes(?Coaches $coachActif): array
    {
        $annee = $this->settingsService->anneeCourante();

        $etiquetteAnne = AnneeEnum::numeroToAnnee();

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

    /**
     * @param Object|Coaches|null $coach
     * @return array
     */
    public function compileEquipesAnneeEnCours($coach): array
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
     * @throws \Exception
     */
    public function feuilleDequipeComplete(Teams $equipe, PlayerService $playerService): array
    {
        $pdata = [];

        $players = $equipe->getJoueurs();

        $players = $this->sortPlayers($players);

        $pdata = $playerService->ligneJoueur($players->toArray());

        $tdata = $this->calculsInducementEquipe($equipe, $playerService);

        return  [
            'players' => $players,
            'team' => $equipe,
            'pdata' => $pdata,
            'tdata' => $tdata,
            'annee' => $this->settingsService->anneeCourante(),
            'niveauStade' => NiveauStadeEnum::numeroVersNiveauDeStade(),
            'compteur' => $this->compteLesjoueurs($equipe)
        ];
    }

    /**
     * @param Teams $equipe
     * @param PlayerService $playerService
     * @return array
     */
    public function calculsInducementEquipe(Teams $equipe, PlayerService $playerService) : array
    {
        $inducement = $this->inducementService->valeurInducementDelEquipe($equipe);

        $tdata['playersCost'] = $playerService->coutTotalJoueurs($equipe);
        $tdata['rerolls'] = $inducement['rerolls'];
        $tdata['pop'] = $inducement['pop'];
        $tdata['asscoaches'] = $inducement['asscoaches'];
        $tdata['cheerleader'] = $inducement['cheerleader'];
        $tdata['apo'] = $inducement['apo'];
        $tdata['tv'] = $this->equipeGestionService->tvDelEquipe($equipe, $playerService);
        if($equipe->getRuleset() == RulesetEnum::BB_2020) {
            if($equipe->getSpecialRulechoosed() == null && in_array($equipe->getRace()->getName(), $this->raceChoice)) {
                $tdata['reglesSpeciales'] = 'choose';
            } else {
                $tdata['reglesSpeciales']  = $this->parseReglesSpeciales($equipe);
            }
        }
        return $tdata;
    }

    public function parseReglesSpeciales(Teams $equipe) : String
    {
        $race = $equipe->getRace();

        $text = '';

        if(!in_array($race->getName(),$this->raceChoice)) {

            if(!$race->getSpecialRule()->isEmpty()) {
                /** @var SpecialRule  $specialRule */
                foreach ($race->getSpecialRule() as $specialRule) {
                    $text .= $specialRule->getName();
                    $text .= ', ';
                }
            }

            return substr($text, 0, strlen($text)-2);
        }

        return $equipe->getSpecialRulechoosed() == null ? 'A choisir' : $equipe->getSpecialRulechoosed();
    }

    /**
     * @param Request $request
     * @param string $logoDirectory
     * @param Teams $equipe
     * @throws ImageResizeException
     */
    public function enregistreLogo(Request $request, string $logoDirectory, Teams $equipe) : void
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
            $coutEtnbr = $this->inducementService->ajoutInducement($equipe, $type, $playerService, $this->equipeGestionService);
        } else {
            $coutEtnbr = $this->inducementService->supprInducement($equipe, $type, $playerService, $this->equipeGestionService);
        }
        $tv = $this->equipeGestionService->tvDelEquipe($equipe, $playerService);

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
     * @param string $logoDirectory
     */
    public function supprimerLogo(Teams $equipe, string $logoDirectory) : void
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($logoDirectory . '/' . $equipe->getLogo());

        $equipe->setLogo(null);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        $this->doctrineEntityManager->refresh($equipe);
    }

    public function calculBonusPoulpi(Teams $equipe): float
    {
        $bonusTotal = 0;

        /** @var Matches $match */
        foreach ($this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe) as $match) {
            list($bonus, $pts) = $this->calculBonusPourUnMatchPoulpi($equipe, $match);
            $bonusTotal += $bonus;
        }

        return $bonusTotal;
    }

    /**
     * @param $equipe
     * @param Matches $match
     * @return float|float[]|int[]
     */
    public function calculBonusPourUnMatchPoulpi($equipe, Matches $match)
    {
        if ($match->getTeam1() === $equipe) {
            $scoreEquipeAdv = $match->getScoreClassementTeam2();
            $score = $match->getScoreClassementTeam1();
        } else {
            $scoreEquipeAdv = $match->getScoreClassementTeam1();
            $score = $match->getScoreClassementTeam2();
        }

        if($scoreEquipeAdv > $score) {
          $bonus = ($scoreEquipeAdv - $score) / 10;
        } else {
          $bonus = ($score - $scoreEquipeAdv) / 10;
        }

        $pointsPerdus = 0;

        if($bonus > 5) {
            $pointsPerdus = $bonus - 5;
            $bonus = 5;
        }

        if($bonus < -5) {
            $pointsPerdus = -(abs($bonus) - 5);
            $bonus = -5;
        }

        $bonus = $bonus > 5 ? 5 : $bonus;
        $bonus = $bonus < -5 ? -5 : $bonus;

        return [$bonus, $pointsPerdus];
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function compteLesjoueurs(Teams $equipe): array
    {
        $collectionJoueurs = $equipe->getJoueurs();

        $compteJoueurActif =  $compteJournalier = $compteJoueurblesses = 0;

        if (!$collectionJoueurs->isEmpty()) {
            $joueursActif = $collectionJoueurs->filter(
                function ($joueur) {
                    /** @var Players $joueur */
                    if(
                        !$joueur->getJournalier()
                        && $joueur->getInjRpm() == 0
                        && $joueur->getStatus() != 7
                        && $joueur->getStatus() != 8
                    ) {
                        return true;
                    }

                    return false;
                }
            );

            $journaliers = $collectionJoueurs->filter(
                function ($joueur) {
                    /** @var Players $joueur */
                    if(
                        $joueur->getJournalier()
                        && $joueur->getInjRpm() == 0
                        && $joueur->getStatus() != 7
                        && $joueur->getStatus() != 8
                    ) {
                        return true;
                    }

                    return false;
                }
            );

            $blesses = $collectionJoueurs->filter(
                function ($joueur) {
                    /** @var Players $joueur */
                    if(
                        !$joueur->getJournalier()
                        && $joueur->getInjRpm() == 1
                        && $joueur->getStatus() != 7
                        && $joueur->getStatus() != 8
                    ) {
                        return true;
                    }

                    return false;
                }
            );

            $compteJoueurActif = !$joueursActif->isEmpty() ? $joueursActif->count() : 0;
            $compteJournalier = !$journaliers->isEmpty() ? $journaliers->count() : 0;
            $compteJoueurblesses = !$blesses->isEmpty() ? $blesses->count() : 0;
        }


        return [
            'actif' => $compteJoueurActif,
            'journalier' => $compteJournalier,
            'blesses' => $compteJoueurblesses
        ];
    }

    /**
     * @param Collection $joueurs
     * @return ArrayCollection
     * @throws \Exception
     */
    private function sortPlayers(Collection $joueurs): ArrayCollection
    {
        $iterator = $joueurs->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getNr() < $b->getNr()) ? -1 : 1;
        });
        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @param Teams $equipe
     * @return array
     * @throws \Exception
     */
    public function getListActivePlayers(Teams $equipe): array
    {
        $joueurCollection = $equipe->getJoueurs();

        if (isset($joueurCollection)) {
            $joueurCollection = $this->sortPlayers($joueurCollection);
            $joueurCollection = $joueurCollection->filter(
                function ($joueur) {
                    /** @var Players $joueur */
                    if(
                        $joueur->getInjRpm() == 0
                        && $joueur->getStatus() != 7
                        && $joueur->getStatus() != 8
                    ) {
                        return true;
                    }

                    return false;
                }
            );
        }

        return $joueurCollection->toArray();
    }

    public function maxScore($point, $match, $equipe)
    {
        if ($match->getTeam1() === $equipe) {
            $scoreEquipeAdv = $match->getScoreClassementTeam2();
            $score = $match->getScoreClassementTeam1();
        } else {
            $scoreEquipeAdv = $match->getScoreClassementTeam1();
            $score = $match->getScoreClassementTeam2();
        }

        if($score > $scoreEquipeAdv) {
            $point = -$point;
        }

        return $point;
    }

    public function afficheCalculScore(Teams $equipe)
    {
        $scoreCalculList = $this->doctrineEntityManager->getRepository(ScoreCalcul::class)->findBy(['teams' => $equipe->getTeamId()]);

        $result = [];

        /** @var ScoreCalcul $scoreCalcul */
        foreach ($scoreCalculList as $scoreCalcul) {
            if($equipe === $scoreCalcul->getMatchCible()->getTeam1()) {
                $ligneResult['scoreDebutMatch'] = $scoreCalcul->getMatchCible()->getScoreClassementTeam1();
                $ligneResult['equipeAdv'] = $scoreCalcul->getMatchCible()->getTeam2();
                $ligneResult['scoreAdv'] = $scoreCalcul->getMatchCible()->getScoreClassementTeam2();
            } else {
                $ligneResult['scoreDebutMatch'] = $scoreCalcul->getMatchCible()->getScoreClassementTeam2();
                $ligneResult['scoreAdv'] = $scoreCalcul->getMatchCible()->getScoreClassementTeam1();
                $ligneResult['equipeAdv'] = $scoreCalcul->getMatchCible()->getTeam1();
            }

            $points = $this->settingsService->pointsEnCours($this->settingsService->anneeCourante());

            $resultat = $this->resultatDuMatch($equipe, $scoreCalcul->getMatchCible());

            $ligneResult['mouvement'] = 0;

            $ligneResult['mouvement'] += $resultat['win'] == 1 ? $points[0] : 0;
            $ligneResult['mouvement'] += $resultat['draw'] == 1 ? $points[1] : 0;
            $ligneResult['mouvement'] += $resultat['loss'] == 1 ? $points[2] : 0;

            $ligneResult['resultat'] = '';

            $ligneResult['resultat'] = $resultat['win'] == 1 ? 'victoire' : $ligneResult['resultat'];
            $ligneResult['resultat'] = $resultat['draw'] == 1 ? 'Egalite': $ligneResult['resultat'];
            $ligneResult['resultat'] = $resultat['loss'] == 1 ? 'défaite': $ligneResult['resultat'];

            $ligneResult['bonus'] = $scoreCalcul->getBonus();

            $ligneResult['mouvementTotal'] = $ligneResult['mouvement'] + $ligneResult['bonus'];

            $ligneResult['ajustement'] = $scoreCalcul->getLostPoint();

            $ligneResult['match'] = $scoreCalcul->getMatchCible();

            $result[] = $ligneResult;
        }

        return $result;
    }
}
