<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Sponsors;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class SponsorAjouteTest  extends TestCase
{
    /**
     * @test
     */
    public function le_text_s_affiche_bien()
    {
        $sponsor = new Sponsors();
        $sponsor->setName("TEST SPONSOR");

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('test team');
        $equipeMock->method('getTeamId')->willReturn(1);
        $equipeMock->method('getSponsor')->willReturn($sponsor);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class),
            $envMock
        );

        $messageActual = $infosServiceTest->sponsorAjoute($equipeMock);

        $this->assertEquals('TEST SPONSOR a décidé de sponsoriser <a href="/team/1">test team</a> !',  $messageActual->getMessages());
    }

}