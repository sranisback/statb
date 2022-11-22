<?php

namespace App\Service;

use App\Entity\Citations;
use App\Entity\Coaches;
use Doctrine\ORM\EntityManagerInterface;

class CitationService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param array<string,string> $datas
     */
    public function enregistrerCitation(Citations $citation): void
    {
        $this->doctrineEntityManager->persist($citation);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($citation);
    }


    /**
     * @return Citations
     */
    public function tirerCitationAuHasard():Citations
    {
        $citations = $this->doctrineEntityManager->getRepository(Citations::class)->findAll();

        if ($citations) {
            $nbrAuHasard = rand(0, count($citations) - 1);

            return  $citations[$nbrAuHasard];
        }

        return new Citations();
    }
}

