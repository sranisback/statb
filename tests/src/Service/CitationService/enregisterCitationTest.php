<?php


namespace App\Tests\src\Service\CitationService;


use App\Entity\Citations;
use App\Service\CitationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class enregisterCitationTest extends TestCase
{
    /**
     * @test
     */
    public function la_citation_est_bien_enregistree()
    {
        $citation = new Citations();

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())->method('persist')->with($citation);
        $objectManager->expects($this->once())->method('flush');
        $objectManager->expects($this->once())->method('refresh')->with($citation);

        $citationService = new CitationService($objectManager);

        $citationService->enregistrerCitation($citation);
    }
}