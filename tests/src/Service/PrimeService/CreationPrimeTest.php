<?php


namespace App\Tests\src\Service\PrimeService;


use App\Entity\Players;
use App\Entity\Primes;
use App\Service\PrimeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CreationPrimeTest extends TestCase
{

    /**
     * @test
     */
    public function la_prime_est_bien_cree()
    {
        $playersMock = $this->createMock(Players::class);

        $primeRepoMock = $this->getMockBuilder(Primes::class)
            ->setMethods(['FindOneBy'])
            ->getMock();
        $primeRepoMock->method('FindOneBy')->willReturn(
            false
        );

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['FindOneBy'])
            ->getMock();
        $playerRepoMock->method('FindOneBy')->willReturn(
            $playersMock
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn(
            $this->returnCallback(
                function ($entityName) use ($playerRepoMock, $primeRepoMock) {
                    if ($entityName === 'App\Entity\Players') {
                        return $playerRepoMock;
                    }

                    if ($entityName === 'App\Entity\Primes') {
                        return $primeRepoMock;
                    }

                    return true;
                }
            )
        );

        $primeService = new PrimeService(
            $objectManager
        );

        $datasDuForm = [
            'montant' => 30000,
            'players' => 1
        ];

        /** @var Primes $prime */
        $prime = $primeService->creationPrime($datasDuForm);

        $this->assertEquals(30000, $prime->getMontant());
    }

    /**
     * @test
     */
    public function les_primes_sont_ajoutees()
    {
        $playersMock = $this->createMock(Players::class);

        $prime = new Primes();
        $prime->setMontant(10000);

        $primeRepoMock = $this->getMockBuilder(Primes::class)
            ->setMethods(['FindOneBy'])
            ->getMock();
        $primeRepoMock->method('FindOneBy')->willReturn(
            $prime
        );

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['FindOneBy'])
            ->getMock();
        $playerRepoMock->method('FindOneBy')->willReturn(
            $playersMock
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn(
            $this->returnCallback(
                function ($entityName) use ($playerRepoMock, $primeRepoMock) {
                    if ($entityName === 'App\Entity\Players') {
                        return $playerRepoMock;
                    }

                    if ($entityName === 'App\Entity\Primes') {
                        return $primeRepoMock;
                    }

                    return true;
                }
            )
        );

        $primeService = new PrimeService(
            $objectManager
        );

        $datasDuForm = [
            'montant' => 30000,
            'players' => 1
        ];

        /** @var Primes $prime */
        $prime = $primeService->creationPrime($datasDuForm);

        $this->assertEquals(40000, $prime->getMontant());
    }
}