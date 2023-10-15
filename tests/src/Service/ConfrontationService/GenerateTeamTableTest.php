<?php


namespace App\Tests\src\Service\ConfrontationService;


use App\Entity\Teams;
use App\Service\ConfrontationService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class GenerateTeamTableTest extends TestCase
{
    private ConfrontationService $confrontationService;

    private SettingsService $settingsService;

    private $objectManager;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->settingsService = $this->createMock(SettingsService::class);

        $this->confrontationService = new ConfrontationService(
            $this->objectManager,
            $this->settingsService
        );
    }

    /**
     * @test
     */
    public function generateTeamTableTest_Ok()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getTeamId')->willReturn(0);
        $equipeMock0->method('getName')->willReturn('team1');

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getTeamId')->willReturn(1);
        $equipeMock1->method('getName')->willReturn('team2');

        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getTeamId')->willReturn(2);
        $equipeMock2->method('getName')->willReturn('team3');

        $equipeMock3 = $this->createMock(Teams::class);
        $equipeMock3->method('getTeamId')->willReturn(3);
        $equipeMock3->method('getName')->willReturn('team4');

        $equipeRepoMock = $this->createMock(ObjectRepository::class);
        $equipeRepoMock->method('findBy')->willReturn(
            [$equipeMock0, $equipeMock1, $equipeMock2, $equipeMock3]
        );

        $this->objectManager->method('getRepository')->willReturn($equipeRepoMock);

        $tableExpected = [
            'team1' => [
                'team1' => 0,
                'team2' => 0,
                'team3' => 0,
                'team4' => 0
            ],
            'team2' => [
                'team1' => 0,
                'team2' => 0,
                'team3' => 0,
                'team4' => 0
            ],
            'team3' => [
                'team1' => 0,
                'team2' => 0,
                'team3' => 0,
                'team4' => 0

            ],
            'team4' => [
                'team1' => 0,
                'team2' => 0,
                'team3' => 0,
                'team4' => 0

            ]
        ];

        $this->assertEquals($tableExpected, $this->confrontationService->generateTeamTable(5));
    }
}