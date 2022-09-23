<?php


namespace App\Tests\src\Service\ExportService;


use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\ExportService;
use App\Service\InducementService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GeneratePdfTest extends TestCase
{

    /**
     * @test
     */
    public function generatePdfTest()
    {
        $joueur0 = new Players();
        $joueur1 = new Players();
        $joueur2 = new Players();
        $joueur3 = new Players();
        $joueur4 = new Players();

        $joueurList = new ArrayCollection();
        $joueurList->add($joueur0);
        $joueurList->add($joueur1);
        $joueurList->add($joueur2);
        $joueurList->add($joueur3);
        $joueurList->add($joueur4);

        $equipe = new Teams();
        $equipe->setJoueurs($joueurList);
        $equipe->setName('TEST');

        $retour = [
            'NbrMatch' => 1,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 1,
            'agg' => 0,
            'bonus' => 0,
            'exp' => 0
        ];

        $equipeRepo = $this->getMockBuilder(Teams::class)
            ->addMethods(['find'])
            ->getMock();

        $equipeRepo->method('find')->willReturn($equipe);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($equipeRepo);

        $playerService = $this->createMock(PlayerService::class);
        $playerService->method('toutesLesCompsdUnJoueur')->willReturn('<text class="test-primary">Test</text>, <text class="text-danger">Test</text>, ');
        $playerService->method('actionsDuJoueur')->willReturn($retour);
        $playerService->method('xpDuJoueur')->willReturn(5);
        $playerService->method('valeurDunJoueur')->willReturn(15);
        $playerService->method('statutDuJoueur')->willReturn('');
        $playerService->method('coutTotalJoueurs')->willReturn(75);

        $inducementService = $this->createMock(InducementService::class);
        $inducementService->method('valeurInducementDelEquipe')->willReturn(
            [
                'rerolls' => 0,
                'pop' => 0,
                'asscoaches' => 0,
                'cheerleader' => 0,
                'apo' => 0,
                'total' => 0
            ]
        );

        $equipeGestionService = $this->createMock(EquipeGestionService::class);
        $equipeGestionService->method('tvDelEquipe')->willReturn(150);

        $equipeService = $this->createMock(EquipeService::class);
        $equipeService->method('compteLesjoueurs')->willReturn(['actif' => 5, 'journalier' => 0, 'blesses' => 0]);

        $exportService = new ExportService(
            $objectManager,
            $playerService,
            $inducementService,
            $equipeGestionService,
            $equipeService
        );

        $actual = $exportService->generatePdf("1");

        $pData = [
            'agg' => 0,
            'bonusXP' => 0,
            'cas' => 0,
            'cost' => 15,
            'cp' => 0,
            'exp' => 0,
            'int' => 0,
            'mvp' => 1,
            'nbrm' => 1,
            'pid' => 0,
            'skill' => '<text class="test-primary">Test</text>, <text class="text-danger">Test</text>',
            'spp' => 5,
            'status' => "",
            'td' => 0
        ];

        $tData = [
            'apo' => 0,
            'asscoaches' => 0,
            'cheerleader' => 0,
            'playersCost' => 75,
            'pop' => 0,
            'rerolls' => 0,
            'total' => 0,
            'tv' => 150
        ];

        $expected = [
            'nom' => $equipe->getName(),
            'players' => $joueurList,
            'pdata' => [$pData,$pData,$pData,$pData,$pData],
            'tdata' => $tData,
            'team' => $equipe,
            'compteur' => ['actif' => 5, 'journalier' => 0, 'blesses' => 0]
        ];

        $this->assertNotNull($actual);
        $this->assertEquals($expected, $actual);
    }

}