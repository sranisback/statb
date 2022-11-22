<?php


namespace App\Tests\src\Service\SponsorService;


use App\Entity\Infos;
use App\Entity\Sponsors;
use App\Entity\Teams;
use App\Service\InfosService;
use App\Service\SponsorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class AffecteUnSponsorTest extends TestCase
{
    /**
     * @test
     */
    public function sponsor_correctement_affecte()
    {
        $equipe = new Teams();

        $sponsor = new Sponsors();
        $sponsor->setName("Test Company");

        $sponsorRepoMock = $this->createMock(ObjectRepository::class);
        $sponsorRepoMock->method('findAll')->willReturn([$sponsor]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($sponsorRepoMock);
        $objectManager->expects($this->once())->method('persist')->will($this->returnCallback(function ($equipe) use ($sponsor) {
            $this->assertEquals($sponsor, $equipe->getSponsor());
        }));
        $objectManager->expects($this->once())->method('flush');
        $objectManager->expects($this->once())->method('refresh')->will($this->returnCallback(function ($equipe) use ($sponsor) {
            $this->assertEquals($sponsor, $equipe->getSponsor());
        }));

        $infosService = $this->createMock(InfosService::class);
        $infosService->expects($this->once())->method('sponsorAjoute')->will($this->returnCallback(function ($equipe) use ($sponsor) {
            $this->assertEquals($sponsor, $equipe->getSponsor());
        }))->willReturn(new Infos());

        $sponsorService = new SponsorService(
            $objectManager,
            $infosService
        );

        $sponsorService->affecteUnSponsor($equipe);

    }
}