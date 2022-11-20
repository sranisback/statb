<?php


namespace App\Tests\src\Service\CitationService;


use App\Entity\Citations;
use App\Entity\Coaches;
use App\Repository\CitationsRepository;
use App\Service\CitationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class TirerCitationAuHasardTest extends TestCase
{
    /**
     * @test
     */
    public function il_n_y_a_pas_de_citations()
    {
        $citationRepos = $this->createMock(CitationsRepository::class);
        $citationRepos->method('findAll')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($citationRepos);

        $citationService = new CitationService($objectManager);

        $this->assertEquals(new Citations(),$citationService->tirerCitationAuHasard());
    }

    /**
     * @test
     */
    public function un_objet_citation_est_retourne()
    {
        $coach = $this->createMock(Coaches::class);
        $coach->method('getCoachId')->willReturn(1);

        $citation = new Citations();
        $citation->setCitation("Bla");
        $citation->setCoachId($coach);

        $citationRepos = $this->createMock(ObjectRepository::class);
        $citationRepos->method('findAll')->willReturn([$citation]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($citationRepos);

        $citationService = new CitationService($objectManager);

        $citation = $citationService->tirerCitationAuHasard();

        $this->assertEquals("Bla", $citation->getCitation());
        $this->assertEquals(1, $citation->getCoachId()->getCoachId());
    }
}