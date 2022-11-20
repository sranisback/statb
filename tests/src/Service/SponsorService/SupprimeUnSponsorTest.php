<?php


namespace App\Tests\src\Service\SponsorService;


use App\Entity\Infos;
use App\Entity\Sponsors;
use App\Entity\Teams;
use App\Service\InfosService;
use App\Service\SponsorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SupprimeUnSponsorTest extends TestCase
{
    /**
     * @test
     */
    public function sponsor_correctement_supprime()
    {
        $sponsor = new Sponsors();
        $sponsor->setName("Test Company");

        $equipe = new Teams();
        $equipe->setSponsor($sponsor);


        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())->method('persist')->will($this->returnCallback(function ($equipe) use ($sponsor) {
            $this->assertNull($equipe->getSponsor());
        }));
        $objectManager->expects($this->once())->method('flush');
        $objectManager->expects($this->once())->method('refresh')->will($this->returnCallback(function ($equipe) use ($sponsor) {
            $this->assertNull($equipe->getSponsor());
        }));

        $infosService = $this->createMock(InfosService::class);
        $infosService->expects($this->once())->method('sponsorSupprime')->will($this->returnCallback(function ($equipe) use ($sponsor) {
            $this->assertNull($equipe->getSponsor());
        }))->willReturn(new Infos());

        $sponsorService = new SponsorService(
            $objectManager,
            $infosService
        );

        $sponsorService->supprimeUnSponsor($equipe);

    }
}