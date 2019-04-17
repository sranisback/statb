<?php
/**
 * Created by PhpStorm.
 * User: Sran_isback
 * Date: 22/02/2019
 * Time: 17:17
 */

namespace App\Tests\src\Service\PlayerService;


use App\Entity\Players;
use App\Entity\Teams;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class numeroLibreDelEquipe extends TestCase
{
    /**
     * @test
     */
    public function est_ce_que_le_numero_du_joueur_est_bien_renvoye(){
        $team = new Teams();
        $team->setYear(3);

        $player0 = new Players();
        $player0->setNr(1);
        $player0->setOwnedByTeam($team);

        $player1 = new Players();
        $player1->setNr(2);
        $player1->setOwnedByTeam($team);

        $player2 = new Players();
        $player2->setNr(3);
        $player2->setOwnedByTeam($team);

        $player3 = new Players();
        $player3->setNr(5);
        $player3->setOwnedByTeam($team);

        $player4 = new Players();
        $player4->setNr(6);
        $player4->setOwnedByTeam($team);

        $listeJoueur = [$player0, $player1, $player2, $player3, $player4];

        $playerRepo = $this->createMock(ObjectRepository::class);

        $playerRepo->expects($this->any())->method('findBy')->willReturn($listeJoueur);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->any())->method('getRepository')->willReturn($playerRepo);

        $test = new PlayerService($entityManager);

        $this->assertEquals(4,$test->numeroLibreDelEquipe($team));
    }

}