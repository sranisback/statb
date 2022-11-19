<?php


namespace App\Tests\src\Service\PrimeService;


use App\Entity\Primes;
use App\Service\InfosService;
use App\Service\PrimeService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MontrePrimeEnCoursTest extends TestCase
{

    /**
     * @test
     */
    public function les_primes_sont_montrees()
    {
        $prime = new Primes();

        $primeRepoMock = $this->getMockBuilder(Primes::class)
            ->addMethods(['listePrimeEnCours'])
            ->getMock();
        $primeRepoMock->method('listePrimeEnCours')->willReturn(
            [$prime]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($primeRepoMock);

        $primeService = new PrimeService(
            $objectManager,
            $this->createMock(InfosService::class),
            $this->createMock(SettingsService::class)
        );

        $resultat = $primeService->montrePrimeEnCours();

        $this->assertEquals([$prime], $resultat);
    }
}