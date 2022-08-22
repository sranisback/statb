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
    public function enregistrerCitation(array $datas): void
    {
        $citation = new Citations;

        $citation->setCitation($datas['citation']);

        $citation->setCoachId(
            $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(['coachId' => $datas['coachId']])
        );

        $this->doctrineEntityManager->persist($citation);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($citation);
    }
}
