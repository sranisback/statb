<?php

namespace App\Service;

use App\Entity\MatchData;
use App\Entity\Matches;
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
        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnneClassementChrono(
            $annee
        );

        $matchesAafficher = $this->cinqPremierMatches($matches);

        return $matchesAafficher;
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

    /**
     * @param int $annee
     * @param mixed $type
     * @param mixed $teamorplayer
     * @param int $limit
     * @return array
     */
    public function sousClassements($annee, $type, $teamorplayer, $limit)
    {
        $class = '';
        $title = '';

        $matchData = $this->doctrineEntityManager->getRepository(MatchData::class)->SClassement(
            $annee,
            $type,
            $teamorplayer,
            $limit
        );

        if ($teamorplayer == 'player') {
            switch ($type) {
                case 'bash':
                    $title = 'Le Bash Lord - Record CAS';
                    $class = 'class_bash';

                    break;

                case 'td':
                    $title = 'Le Marqueur - Record TD';
                    $class = 'class_td';

                    break;

                case 'xp':
                    $title = 'Le Meilleur - Record SPP';
                    $class = 'class_xp';

                    break;

                case 'pass':
                    $title = 'La Main d\'or - Record Passes';
                    $class = 'class_pass';

                    break;

                case 'foul':
                    $title = 'Le Tricheur - Record Fautes';
                    $class = 'class_foul';
                    break;
            }

            return [
                'players' => $matchData,
                'title' => $title,
                'class' => $class,
                'type' => $type,
                'teamorplayer' => $teamorplayer,
                'limit' => $limit,
            ];
        } else {
            switch ($type) {
                case 'bash':
                    $title = 'Les plus méchants';
                    $class = 'class_Tbash';

                    break;

                case 'td':
                    $title = 'Le plus de TD';
                    $class = 'class_Ttd';

                    break;


                case 'dead':
                    $title = 'Fournisseurs de cadavres';
                    $class = 'class_Tdead';
                    break;

                case 'foul':
                    $title = 'Les tricheurs';
                    $class = 'class_Tfoul';
                    break;
            }

            return [
                'teams' => $matchData,
                'title' => $title,
                'class' => $class,
                'type' => $type,
                'teamorplayer' => $teamorplayer,
                'limit' => $limit,
            ];
        }
    }

    public function totalCas($annee)
    {
        $score = $this->doctrineEntityManager->getRepository(MatchData::class)->totalcas($annee);
        $nbrMatches = count($this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnne($annee));

        if ($nbrMatches === 0) {
            $moyenne = 0;
        } else {
            $moyenne = round($score/$nbrMatches, 2);
        }

        return [
            'score' => $score,
            'nbrMatches' => $nbrMatches,
            'moyenne' => $moyenne
        ];
    }
}
