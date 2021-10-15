<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class confrontationPourDeuxCoachesTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_resultat_de_deux_coaches_est_bien_retournee(): void
    {
        $coachTest0 = $this->createMock(Coaches::class);
        $coachTest0->method('getUsername')->willReturn('coach 0');
        $coachTest0->method('getCoachId')->willReturn(0);

        $coachTest1 = $this->createMock(Coaches::class);
        $coachTest1->method('getUsername')->willReturn('coach 1');
        $coachTest1->method('getCoachId')->willReturn(1);

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);
        $equipeTest1 = new Teams();
        $equipeTest1->setOwnedByCoach($coachTest1);

        $match0 = new Matches();
        $match0->setTeam1($equipeTest0);
        $match0->setTeam2($equipeTest1);
        $match1 = new Matches();
        $match1->setTeam1($equipeTest1);
        $match1->setTeam2($equipeTest0);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            [$match0, $match1]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchesRepoMock);

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1]
        );

        $tableauAttendu = [
            '50%',
            1,
            0,
            1,
            1
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationPourDeuxCoaches($coachTest0, $coachTest1, $equipeServiceMock)
        );
    }


    /**
     * @test
     */
    public function il_n_y_a_pas_de_matchs_mais_des_equipes_et_coachs(): void
    {
        $coachTest0 = new Coaches();
        $coachTest0->setUsername('coach 0');
        $coachTest1 = new Coaches();
        $coachTest1->setUsername('coach 1');

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);
        $equipeTest1 = new Teams();
        $equipeTest1->setOwnedByCoach($coachTest1);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchesRepoMock);

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $tableauAttendu = [
            'N/A',
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationPourDeuxCoaches(
                $coachTest0,
                $coachTest1,
                $this->createMock(EquipeService::class)
            )
        );
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $coachTest0 = new Coaches();
        $coachTest0->setUsername('coach 0');
        $coachTest1 = new Coaches();
        $coachTest1->setUsername('coach 1');


        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchesRepoMock);

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $tableauAttendu = [
            'N/A',
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationPourDeuxCoaches(
                $coachTest0,
                $coachTest1,
                $this->createMock(EquipeService::class)
            )
        );
    }

    /**
     * @test
     */
    public function une_paire_de_coach_ne_se_sont_pas_rencontre(): void
    {
        $coachTest0 = new Coaches();
        $coachTest0->setUsername('coach 0');
        $coachTest1 = new Coaches();
        $coachTest1->setUsername('coach 1');

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchesRepoMock);

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $tableauAttendu = [
            'N/A',
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationPourDeuxCoaches(
                $coachTest0,
                $coachTest1,
                $this->createMock(EquipeService::class)
            )
        );
    }
    /**
     * @test
     */
    public function les_coaches_ont_plusieurs_equipes(): void
    {
        $coachTest0 = $this->createMock(Coaches::class);
        $coachTest0->method('getUsername')->willReturn('coach 0');
        $coachTest0->method('getCoachId')->willReturn(0);

        $coachTest1 = $this->createMock(Coaches::class);
        $coachTest1->method('getUsername')->willReturn('coach 1');
        $coachTest1->method('getCoachId')->willReturn(1);

        $equipeTest0a = new Teams();
        $equipeTest0a->setOwnedByCoach($coachTest0);
        $equipeTest0b = new Teams();
        $equipeTest0b->setOwnedByCoach($coachTest0);
        $equipeTest1a = new Teams();
        $equipeTest1a->setOwnedByCoach($coachTest1);
        $equipeTest1b = new Teams();
        $equipeTest1b->setOwnedByCoach($coachTest1);

        $match0 = new Matches();
        $match0->setTeam1($equipeTest0a);
        $match0->setTeam2($equipeTest1a);
        $match1 = new Matches();
        $match1->setTeam1($equipeTest0b);
        $match1->setTeam2($equipeTest1b);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            [$match0, $match1]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchesRepoMock);

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1]
        );

        $tableauAttendu = [
            '50%',
            1,
            0,
            1,
            1
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationPourDeuxCoaches($coachTest0, $coachTest1, $equipeServiceMock)
        );
    }

}