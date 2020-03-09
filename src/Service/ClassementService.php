<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class ClassementService
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private \Doctrine\ORM\EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
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

        $total = sizeof($matches);
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
        $tdAverage = 0;
        /** @var Matches $match */
        foreach ($this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe) as $match) {
            if ($match->getTeam1() === $equipe) {
                $tdMis += $match->getTeam1Score();
                $tdPris += $match->getTeam2Score();
            } else {
                $tdMis += $match->getTeam2Score();
                $tdPris += $match->getTeam1Score();
            }
            $tdAverage = $tdMis - $tdPris;
        }

        return [
            'equipe' => $equipe,
            'tdMis' => $tdMis,
            'tdPris' => $tdPris,
            'tdAverage' => $tdAverage
        ];
    }

    /**
     * @param int $annee
     * @return array
     */
    public function classementDetailScoreGen(int $annee): array
    {
        $tableDetail = [];

        /** @var Teams $equipe */
        foreach ($this->doctrineEntityManager->getRepository(Teams::class)->findBy(['year' => $annee]) as $equipe) {
            $tableDetail[] = $this->classementDetailScoreDuneEquipe($equipe);
        }

        return $tableDetail;
    }

    /**
     * @return mixed[][]
     */
    public function classementDetail(int $annee): array
    {
        $classementDetail = [];
        $pointsBonus = $this->doctrineEntityManager->getRepository(Teams::class)->pointsBonus($annee);

        foreach ($this->classementDetailScoreGen($annee) as $ligne) {
            foreach ($pointsBonus as $ligneBonus) {
                /** @var Teams $equipe */
                $equipe = $ligne['equipe'];
                if ($equipe->getTeamId() == $ligneBonus['equipeId']) {
                    $classementDetail[] = [
                        'equipe' => $equipe,
                        'tdMis' => $ligne['tdMis'],
                        'tdPris' => $ligne['tdPris'],
                        'tdAverage' => $ligne['tdAverage'],
                        'pts' => $ligneBonus['Bonus'],
                    ];
                }
            }
        }

        return $classementDetail;
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

        $matchData = $this->doctrineEntityManager->getRepository(MatchData::class)->sousClassementJoueur(
            $annee,
            $type,
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            $tableauCompletConfrontation[$coach->getName()] = $this->confrontationTousLesCoaches(
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
    public function confrontationPourDeuxCoaches(Coaches $coach, Coaches $autreCoach, EquipeService $equipeService)
    : array
    {
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
            ).'%';

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
            $tableConfrontation[$autreCoach->getName()] = $this->confrontationPourDeuxCoaches(
                $coach,
                $autreCoach,
                $equipeService
            );
        }

        return $tableConfrontation;
    }
}
