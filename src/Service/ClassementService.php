<?php

namespace App\Service;

use App\Entity\ClassementGeneral;
use App\Entity\Coaches;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Penalite;
use App\Entity\Players;
use App\Entity\Setting;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use Doctrine\ORM\EntityManagerInterface;

class ClassementService
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private \Doctrine\ORM\EntityManagerInterface $doctrineEntityManager;

    private EquipeService $equipeService;

    private MatchDataService $matchDataService;

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
     * @param int $annee
     * @return array<Matches>
     */
    public function cinqDerniersMatchsParAnnee(int $annee): array
    {
        $matches = $this->doctrineEntityManager
            ->getRepository(Matches::class)->tousLesMatchDuneAnneClassementChrono($annee);

        return $this->cinqPremierMatches($matches);
    }

    /**
     * @param array<Matches> $matches
     * @return array<Matches>
     */
    private function cinqPremierMatches(array $matches): array
    {
        $matchesAreatourner = [];

        $total = count($matches);
        if ($total > 5) {
            $total = 5;
        }
        for ($x = 0; $x < $total; $x++) {
            $matchesAreatourner[] = $matches[$x];
        }

        return $matchesAreatourner;
    }

    /**
     * @param int $equipeId
     * @return mixed[]
     */
    public function cinqDerniersMatchsParEquipe(int $equipeId): array
    {
        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs(
            $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId])
        );

        return $this->cinqPremierMatches($matches);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function classementDetailScoreDuneEquipe(Teams $equipe): array
    {
        $tdMis = 0;
        $tdPris = 0;

        $totalSortiePour = 0;
        $totalSortieContre = 0;

        /** @var Matches $match */
        foreach ($this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe) as $match) {
            if ($match->getTeam1() === $equipe) {
                $tdMis += $match->getTeam1Score();
                $tdPris += $match->getTeam2Score();

                $totalSortiePour += $this->matchDataService->nombreDeSortiesDunMatch($match->getTeam1(), $match);
                $totalSortieContre += $this->matchDataService->nombreDeSortiesDunMatch($match->getTeam2(), $match);
            } else {
                $tdMis += $match->getTeam2Score();
                $tdPris += $match->getTeam1Score();

                $totalSortiePour += $this->matchDataService->nombreDeSortiesDunMatch($match->getTeam2(), $match);
                $totalSortieContre += $this->matchDataService->nombreDeSortiesDunMatch($match->getTeam1(), $match);
            }
        }

        return [
            'tdMis' => $tdMis,
            'tdPris' => $tdPris,
            'sortiesPour' => $totalSortiePour,
            'sortiesContre' => $totalSortieContre
        ];
    }

    /**
     * @param int $annee
     * @param string $type
     * @param int $limit
     * @return array<string,mixed>
     */
    public function genereClassementJoueurs(int $annee, string $type, int $limit): array
    {
        $classement = '';
        $titre = '';

        $currentYear = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $ruleset = RulesetEnum::rulesetParAnnee()[$currentYear->getValue()];

        $matchData = $this->doctrineEntityManager->getRepository(MatchData::class)->sousClassementJoueur(
            $annee,
            $type,
            $ruleset,
            $limit
        );

        switch ($type) {
            case 'bash':
                $titre = 'Le Bash Lord - Record CAS';
                $classement = 'class_bash';

                break;

            case 'td':
                $titre = 'Le Marqueur - Record TD';
                $classement = 'class_td';

                break;

            case 'xp':
                $titre = 'Le Meilleur - Record SPP';
                $classement = 'class_xp';

                break;

            case 'pass':
                $titre = 'La Main d\'or - Record Passes';
                $classement = 'class_pass';

                break;

            case 'foul':
                $titre = 'Le Tricheur - Record Fautes';
                $classement = 'class_foul';
                break;

            case 'killer':
                $titre = 'Le Serial Tueur - Record Meurtres';
                $classement = 'class_kill';
                break;

            case 'handi':
                $titre = 'Le Tortionnaire - Record Blessures Graves';
                $classement = 'class_handi';
                break;

            default:
                $titre = 'erreur';
                $classement = 'erreur';
                break;
        }

        return [
            'players' => $matchData,
            'title' => $titre,
            'class' => $classement,
            'type' => $type,
            'limit' => $limit,
            'annee' => $annee
        ];
    }

    /**
     * @param int $annee
     * @param string $type
     * @param int $limit
     * @return array<string,mixed>
     */
    public function genereClassementEquipes(int $annee, string $type, int $limit): array
    {
        $classement = '';
        $titre = '';

        $currentYear = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $ruleset = RulesetEnum::rulesetParAnnee()[$currentYear->getValue()];

        if ($type === 'dead') {
            $matchData = $this->doctrineEntityManager->getRepository(
                Players::class
            )->sousClassementEquipeFournisseurDeCadavre(
                $annee,
                $limit
            );
        } else {
            $matchData = $this->doctrineEntityManager->getRepository(MatchData::class)->sousClassementEquipe(
                $annee,
                $type,
                $ruleset,
                $limit
            );
        }

        switch ($type) {
            case 'bash':
                $titre = 'Les plus méchants';
                $classement = 'class_Tbash';
                break;

            case 'td':
                $titre = 'Le plus de TD';
                $classement = 'class_Ttd';
                break;

            case 'dead':
                $titre = 'Fournisseurs de cadavres';
                $classement = 'class_Tdead';
                break;

            case 'foul':
                $titre = 'Les tricheurs';
                $classement = 'class_Tfoul';
                break;

            case 'killer':
                $titre = 'Les tueurs';
                $classement = 'class_Tkill';
                break;

            default:
                $titre = 'erreur';
                $classement = 'erreur';
                break;
        }

        return [
            'teams' => $matchData,
            'title' => $titre,
            'class' => $classement,
            'type' => $type,
            'limit' => $limit,
            'annee' => $annee
        ];
    }

    /**
     * @param int $annee
     * @return mixed[]
     */
    public function totalCas(int $annee): array
    {
        $score = $this->doctrineEntityManager->getRepository(MatchData::class)->totalcas($annee);
        $nbrMatches = count($this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnne($annee));

        $moyenne = $nbrMatches === 0 ? 0 : round($score / $nbrMatches, 2);
        return [
            'score' => $score,
            'nbrMatches' => $nbrMatches,
            'moyenne' => $moyenne,
        ];
    }

    /**
     * @param EquipeService $equipeService
     * @return array<string, mixed>
     */
    public function genereConfrontationTousLesCoaches(EquipeService $equipeService): array
    {
        $tableauCompletConfrontation = [];

        foreach ($this->doctrineEntityManager->getRepository(Coaches::class)->findAll() as $coach) {
            $tableauCompletConfrontation[$coach->getUsername()] = $this->confrontationTousLesCoaches(
                $coach,
                $equipeService
            );
        }

        return $tableauCompletConfrontation;
    }

    /**
     * @param Coaches $coach
     * @param Coaches $autreCoach
     * @param EquipeService $equipeService
     * @return array<int,mixed>
     */
    public function confrontationPourDeuxCoaches(
        Coaches $coach,
        Coaches $autreCoach,
        EquipeService $equipeService
    ): array {
        $totalResultat = [
            'win' => 0,
            'draw' => 0,
            'loss' => 0,
        ];

        $tableConfrontation = [];

        $listMatches = $this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchsDeDeuxCoach(
            $coach,
            $autreCoach
        );

        if (!empty($listMatches)) {
            foreach ($listMatches as $match) {
                /** @var Matches $match */
                if ($match->getTeam1()->getOwnedByCoach() === $coach) {
                    $tableResultat = $equipeService->resultatDuMatch($match->getTeam1(), $match);
                } else {
                    $tableResultat = $equipeService->resultatDuMatch($match->getTeam2(), $match);
                }
                $totalResultat['win'] += $tableResultat['win'];
                $totalResultat['draw'] += $tableResultat['draw'];
                $totalResultat['loss'] += $tableResultat['loss'];
            }
        }
        if (($totalResultat['loss'] + $totalResultat['win']) > 0) {
            $tableConfrontation[] = round(
                ($totalResultat['win'] / ($totalResultat['loss'] + $totalResultat['win'])) * 100,
                2
            ) . '%';

            $tableConfrontation[] = $totalResultat['win'];
            $tableConfrontation[] = $totalResultat['draw'];
            $tableConfrontation[] = $totalResultat['loss'];
            $tableConfrontation[] = $autreCoach->getCoachId();
        } else {
            $tableConfrontation[] = 'N/A';
        }

        return $tableConfrontation;
    }

    /**
     * @return mixed[][]
     */
    public function confrontationTousLesCoaches(Coaches $coach, EquipeService $equipeService): array
    {
        $tableConfrontation = [];

        /** @var Coaches $autreCoach */
        foreach ($this->doctrineEntityManager->getRepository(Coaches::class)->tousLesAutresCoaches(
            $coach
        ) as $autreCoach) {
            $tableConfrontation[$autreCoach->getUsername()] = $this->confrontationPourDeuxCoaches(
                $coach,
                $autreCoach,
                $equipeService
            );
        }

        return $tableConfrontation;
    }

    /**
     * @param Teams $equipe
     * @param array<int> $point
     * @return array<string, mixed>
     */
    public function ligneClassementGeneral(Teams $equipe, array $point)
    {
        $resultatEquipe = $this->equipeService->resultatsDelEquipe(
            $equipe,
            $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe)
        );

        $points = 0;

        foreach ($resultatEquipe as $typeResultat => $nombreResultat) {
            switch ($typeResultat) {
                case 'win':
                    $points += $nombreResultat * $point[0];
                    break;
                case 'draw':
                    $points += $nombreResultat * $point[1];
                    break;
                case 'loss':
                    $points += $nombreResultat * $point[2];
                    break;
                default:
                    $points += 0;
                    break;
            }
        }

        $bonus = $this->calculPointsBonus($equipe);

        $classementDetail = $this->classementDetailScoreDuneEquipe($equipe);

        return [
            'gagne' => $resultatEquipe['win'],
            'nul' => $resultatEquipe['draw'],
            'perdu' => $resultatEquipe['loss'],
            'pts' => $points,
            'bonus' => $bonus,
            'equipe' => $equipe,
            'tdMis' => $classementDetail['tdMis'],
            'tdPris' => $classementDetail['tdPris'],
            'sortiesPour' => $classementDetail['sortiesPour'],
            'sortiesContre' => $classementDetail['sortiesContre'],
            'penalite' => $this->doctrineEntityManager->getRepository(Penalite::class)->penaliteDuneEquipe($equipe)
        ];
    }

    /**
     * @param int $annee
     * @param array<mixed> $point
     * @return array<mixed>
     */
    public function toutesLesEquipesPourLeClassementGeneral(int $annee, array $point): array
    {
        $table = [];

        foreach ($this->doctrineEntityManager
                     ->getRepository(Teams::class)
                     ->findBy(['year' => $annee, 'retired' => 0]) as $equipe) {
            $table[] = $this->ligneClassementGeneral($equipe, $point);
        }

        return $table;
    }

    public function calculPointsBonus(Teams $equipe): int
    {
        $totalPointBonus = 0;

        /** @var Matches $match */
        foreach ($this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe) as $match) {
            $totalPointBonus = $this->bonusNombreDeSortie($equipe, $match, $totalPointBonus);
            $totalPointBonus = $this->bonusGrosMarqueur($equipe, $match, $totalPointBonus);
            $totalPointBonus = $this->bonusIntrepide($equipe, $match, $totalPointBonus);
            $totalPointBonus = $this->bonusDefense($equipe, $match, $totalPointBonus);
            $totalPointBonus = $this->bonusPetiteDefaite($equipe, $match, $totalPointBonus);
        }

        return $totalPointBonus;
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @param int $totalPointBonus
     * @return int
     */
    private function bonusNombreDeSortie(Teams $equipe, Matches $match, int $totalPointBonus) : int
    {
        if ($this->matchDataService->nombreDeSortiesDunMatch($equipe, $match) >= 4) {
            return $totalPointBonus + 1;
        }

        return $totalPointBonus;
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @param int $totalPointBonus
     * @return int
     */
    private function bonusGrosMarqueur(Teams $equipe, Matches $match, int $totalPointBonus): int
    {
        $tableResult = $this->equipeService->resultatDuMatch($equipe, $match);
        if ($tableResult['win'] == 1 && (($equipe === $match->getTeam1() && $match->getTeam1Score() > 2)
                || ($equipe === $match->getTeam2() && $match->getTeam2Score() > 2))) {
            return $totalPointBonus + 1;
        }
        return $totalPointBonus;
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @param int $totalPointBonus
     * @return int
     */
    private function bonusIntrepide(Teams $equipe, Matches $match, int $totalPointBonus): int
    {
        $tableResult = $this->equipeService->resultatDuMatch($equipe, $match);
        if ($tableResult['win'] == 1 &&
            (($equipe === $match->getTeam1() && (($match->getTv2() / 1_000) - ($match->getTv1() / 1_000) >= 250)) ||
                ($equipe === $match->getTeam2() && (($match->getTv1() / 1_000) - ($match->getTv2() / 1_000) >= 250)))
        ) {
            return $totalPointBonus + 1;
        }
        return $totalPointBonus;
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @param int $totalPointBonus
     * @return int
     */
    private function bonusDefense(Teams $equipe, Matches $match, int $totalPointBonus): int
    {
        $tableResult = $this->equipeService->resultatDuMatch($equipe, $match);
        if ($tableResult['loss'] == 1 &&
            (($equipe == $match->getTeam1() && $match->getTeam2Score() == 1)
                || ($equipe == $match->getTeam2() && $match->getTeam1Score() === 1))) {
            return $totalPointBonus + 1;
        }
        return $totalPointBonus;
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @param int $totalPointBonus
     * @return int
     */
    private function bonusPetiteDefaite(Teams $equipe, Matches $match, int $totalPointBonus): int
    {
        $tableResult = $this->equipeService->resultatDuMatch($equipe, $match);
        if ($tableResult['loss'] == 1 &&
            (($equipe == $match->getTeam1() && $match->getTeam2Score() - $match->getTeam1Score() == 1)
                || ($equipe == $match->getTeam2() && $match->getTeam1Score() - $match->getTeam2Score() == 1))) {
            return $totalPointBonus + 1;
        }
        return $totalPointBonus;
    }

    /**
     * @param array<mixed> $tableauClassementGeneral
     */
    public function sauvegardeClassementGeneral(array $tableauClassementGeneral): void
    {
        foreach ($tableauClassementGeneral as $ligne) {
            $ligneClassement = $this->doctrineEntityManager
                ->getRepository(ClassementGeneral::class)
                ->findOneBy(['equipe' => $ligne['equipe']->getTeamId()]);

            if ($ligneClassement === null) {
                $ligneClassement = new ClassementGeneral();
            }

            $ligneClassement->setGagne($ligne['gagne']);
            $ligneClassement->setEgalite($ligne['nul']);
            $ligneClassement->setPerdu($ligne['perdu']);
            $ligneClassement->setPoints($ligne['pts']);
            $ligneClassement->setBonus($ligne['bonus']);
            $ligneClassement->setEquipe($ligne['equipe']);
            $ligneClassement->setTdPour($ligne['tdMis']);
            $ligneClassement->setTdContre($ligne['tdPris']);
            $ligneClassement->setCasContre($ligne['sortiesContre']);
            $ligneClassement->setCasPour($ligne['sortiesPour']);
            $ligneClassement->setPenalite($ligne['penalite'] === null ? 0 : $ligne['penalite']);

            $this->doctrineEntityManager->persist($ligneClassement);
            $this->doctrineEntityManager->flush();
        }
    }
}
