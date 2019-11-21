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
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param int $annee
     * @return array
     */
    public function cinqDerniersMatchsParAnnee($annee)
    {
        $matches = $this->doctrineEntityManager
            ->getRepository(Matches::class)->tousLesMatchDuneAnneClassementChrono($annee);

        $matchesAafficher = $this->cinqPremierMatches($matches);

        return $matchesAafficher;
    }

    /**
     * @param array $matches
     * @return array
     */
    private function cinqPremierMatches($matches)
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

    public function cinqDerniersMatchsParEquipe($equipeId)
    {

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs(
            $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId])
        );

        $matchesAafficher = $this->cinqPremierMatches($matches);


        return $matchesAafficher;
    }

    /**
     * @param int $annee
     * @param string $type
     * @param int $limit
     * @return array
     */
    public function genereClassementJoueurs(int $annee, string $type, int $limit)
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
        }

        return [
            'players' => $matchData,
            'title' => $titre,
            'class' => $classement,
            'type' => $type,
            'limit' => $limit,
        ];
    }

    /**
     * @param int $annee
     * @param string $type
     * @param int $limit
     * @return array
     */
    public function genereClassementEquipes($annee, $type, $limit)
    {
        $classement = '';
        $titre = '';

        if ($type == 'dead') {
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
        }

        return [
            'teams' => $matchData,
            'title' => $titre,
            'class' => $classement,
            'type' => $type,
            'limit' => $limit,
        ];
    }

    public function totalCas($annee)
    {
        $score = $this->doctrineEntityManager->getRepository(MatchData::class)->totalcas($annee);
        $nbrMatches = count($this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnne($annee));

        if ($nbrMatches === 0) {
            $moyenne = 0;
        } else {
            $moyenne = round($score / $nbrMatches, 2);
        }

        return [
            'score' => $score,
            'nbrMatches' => $nbrMatches,
            'moyenne' => $moyenne,
        ];
    }

    /**
     * @param EquipeService $equipeService
     * @return array
     */
    public function genereConfrontationTousLesCoaches(EquipeService $equipeService)
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
     * @return array
     */
    public function confrontationPourDeuxCoaches(Coaches $coach, Coaches $autreCoach, EquipeService $equipeService)
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
        } else {
            $tableConfrontation[] = 'N/A';
        }

        return $tableConfrontation;
    }

    public function confrontationTousLesCoaches(Coaches $coach, EquipeService $equipeService)
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
