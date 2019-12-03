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
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function creationPrime($coach, $data)
    {
        $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $data['teams']]);

        if ($equipe->getTreasury()>$data['montant']) {
            $prime = new Primes();

            $prime->setCoaches(
                $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(['coachId' => $coach])
            );
            $dateAjoutee = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
            if ($dateAjoutee) {
                $prime->setDateAjoutee($dateAjoutee);
            }
            $prime->setMontant($data['montant']);
            $prime->setPlayers(
                $this->doctrineEntityManager->getRepository(Players::class)->findOneBy(['playerId' => $data['players']])
            );
            $prime->setActif(1);
            $prime->setTeams($equipe);

            if (($equipe->getTreasury() - $prime->getMontant()) > 0) {
                $equipe->setTreasury($equipe->getTreasury() - $prime->getMontant());
            } else {
                $equipe->setTreasury(0);
            }

            $this->doctrineEntityManager->persist($prime);
            $this->doctrineEntityManager->persist($equipe);
            $this->doctrineEntityManager->flush();

            return 'ok';
        }
    }

    public function supprimerPrime($primeId)
    {
        $prime = $this->doctrineEntityManager->getRepository(Primes::class)->findOneBy(['id' => $primeId]);

        /** @var Teams $equipe */
        $equipe = $prime->getTeams();

        $equipe->setTreasury($prime->getMontant() + $equipe->getTreasury());

        $this->doctrineEntityManager->remove($prime);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        return 'ok';
    }

    public function realiserPrime($data)
    {
        $prime = $this->doctrineEntityManager->getRepository(Primes::class)->findOneBy(['id' => $data['Primes']]);

        $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $data['Teams']]);

        $prime->setActif(0);

        $equipe->setTreasury($equipe->getTreasury()+$prime->getMontant());

        $this->doctrineEntityManager->persist($prime);
        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        return 'ok';
    }
}
