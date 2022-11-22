<?php


namespace App\Tests\src\Service\SponsorService;


use App\Entity\Sponsors;
use App\Service\InfosService;
use App\Service\SponsorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class TireSponsorAuHasardTest extends TestCase
{
    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $sponsorRepoMock = $this->createMock(ObjectRepository::class);
        $sponsorRepoMock->method('findAll')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($sponsorRepoMock);

        $sponsorService = new SponsorService(
            $objectManager,
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(new Sponsors(), $sponsorService->tireSponsorAuHasard());
    }

    /**
     * @test
     */
    public function un_nom_de_sponsor_est_tire()
    {
        $sponsor = new Sponsors();
        $sponsor->setName("Test Company");

        $sponsorRepoMock = $this->createMock(ObjectRepository::class);
        $sponsorRepoMock->method('findAll')->willReturn([$sponsor]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($sponsorRepoMock);

        $sponsorService = new SponsorService(
            $objectManager,
            $this->createMock(InfosService::class)
        );

        $sponsorActual  = $sponsorService->tireSponsorAuHasard();

        $this->assertEquals("Test Company", $sponsorActual->getName());
    }
}