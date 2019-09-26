<?php

namespace App\Service;

use App\Entity\Citations;
use App\Entity\Coaches;
use Doctrine\ORM\EntityManagerInterface;

class CitationService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param mixed $datas
     * @return Citations
     */
    public function enregistrerCitation($datas)
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
