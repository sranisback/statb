<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Players;
use App\Entity\Primes;
use App\Entity\Teams;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PrimeService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    public InfosService $infoService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, InfosService $infoService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->infoService = $infoService;
    }

    /**
     * @param array<string,mixed> $data
     * @return Primes
     */
    public function creationPrime(array $data): Primes
    {
        $prime = $this->doctrineEntityManager
            ->getRepository(Primes::class)
            ->findOneBy(['players' => $data['players']]);

        if ($prime === null) {
            $prime = new Primes();
        }

        $prime->setMontant($prime->getMontant() + $data['montant']);
        $prime->setPlayers(
            $this->doctrineEntityManager->getRepository(Players::class)->findOneBy(['playerId' => $data['players']])
        );

        $this->doctrineEntityManager->persist($prime);
        $this->doctrineEntityManager->flush();

        $this->infoService->primeMise($prime);

        return $prime;
    }

    /**
     * @param int $primeId
     * @return string
     */
    public function supprimerPrime(int $primeId): string
    {
        $prime = $this->doctrineEntityManager->getRepository(Primes::class)->findOneBy(['id' => $primeId]);

        $this->doctrineEntityManager->remove($prime);

        $this->doctrineEntityManager->flush();

        return 'ok';
    }

    /**
     * @param array<string,mixed> $data
     * @return string
     */
    public function realiserPrime(array $data): string
    {
        /** @var Primes $prime */
        $prime = $this->doctrineEntityManager->getRepository(Primes::class)->findOneBy(['id' => $data['Primes']]);

        $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $data['Teams']]);

        $equipe->setTreasury($equipe->getTreasury()+$prime->getMontant());

        $prime->setEquipePrime($equipe);

        $this->doctrineEntityManager->persist($prime);
        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        $this->infoService->primeGagnee($prime);

        return 'ok';
    }
}
