<?php

namespace App\Service;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Factory\MatchDataFactory;
use Doctrine\ORM\EntityManagerInterface;

class MatchDataService
{

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private \Doctrine\ORM\EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function creationLigneVideDonneeMatch(Players $joueur, Matches $match): void
    {
        $this->doctrineEntityManager->persist(MatchDataFactory::ligneVide($joueur, $match));

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param MatchData $matchData
     * @return string
     */
    public function lectureLignedUnMatch(MatchData $matchData): string
    {
        $ligneDuMatch = '';

        if ($matchData->getCp() > 0) {
            $ligneDuMatch .= 'CP: ' . $matchData->getCp() . ', ';
        }

        if ($matchData->getTd() > 0) {
            $ligneDuMatch .= 'TD: ' . $matchData->getTd() . ', ';
        }

        if ($matchData->getIntcpt() > 0) {
            $ligneDuMatch .= 'INT: ' . $matchData->getIntcpt() . ',';
        }

        if ($matchData->getBh() > 0) {
            $ligneDuMatch .= 'CAS: ' . $matchData->getBh() . ', ';
        }

        if ($matchData->getSi() > 0) {
            $ligneDuMatch .= ' Blessure(s) grave(s)(CAS) : ' . $matchData->getSi() . ', ';
        }

        if ($matchData->getKi() > 0) {
            $ligneDuMatch .= ' Tué(s)(CAS) : ' . $matchData->getKi() . ', ';
        }

        if ($matchData->getMvp() > 0) {
            $ligneDuMatch .= 'MVP: ' . $matchData->getMvp() . ', ';
        }

        if ($matchData->getAgg() > 0) {
            $ligneDuMatch .= 'AGG: ' . $matchData->getAgg() . ', ';
        }

        if ($matchData->getBonusSpp() > 0) {
            $ligneDuMatch .= 'BONUS: ' . $matchData->getBonusSpp() . ', ';
        }

        return $ligneDuMatch;
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @return int
     */
    public function nombreDeSortiesDunMatch(Teams $equipe, Matches $match) : int
    {
        $total = 0;

        foreach ($this->doctrineEntityManager
                     ->getRepository(Players::class)->listeDesJoueursPourlEquipe($equipe) as $joueur) {
            $ligneMatchData = $this->doctrineEntityManager->getRepository(MatchData::class)->findOneBy([
                'fPlayer' => $joueur->getPlayerId(),
                'fMatch' => $match->getMatchId()
            ]);
            if (!empty($ligneMatchData)) {
                $total += $ligneMatchData->getBh() +
                    $ligneMatchData->getSi() +
                    $ligneMatchData->getKi() +
                    $ligneMatchData->getAgg();
            }
        }

        return $total;
    }
}
