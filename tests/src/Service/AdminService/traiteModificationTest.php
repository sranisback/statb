<?php


namespace App\Tests\src\Service\AdminService;


use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Service\AdminService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class traiteModificationTest extends TestCase
{

    private $objectManager;

    private AdminService $adminService;

    public function setUp(): void
    {
        parent::setUp();

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->adminService = new AdminService(
            $this->objectManager
        );
    }

    /**
     * @test
     */
    public function les_infos_sont_bien_traitees_pour_Coaches_mot_de_passe()
    {
        $coach = new Coaches();
        $coach->setPassword('Not Encripted');

        $coachRepo = $this->getMockBuilder(Coaches::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $coachRepo->method('findOneBy')->willReturn($coach);

        $this->objectManager->method('getRepository')->willReturn($coachRepo);
        $this->objectManager->expects($this->once())->method('flush');

        $request = [
            'name' => 'Password',
            'value'=> '3',
            'pk' => '1'
        ];

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder->method('encodePassword')->willReturn('Encrypted');

        $this->adminService->traiteModification($request, Coaches::class, $encoder);
        $this->assertEquals('Encrypted', $coach->getPassword());
    }


    /**
     * @test
     */
    public function les_infos_sont_bien_traitees_pour_Coaches_Role()
    {
        $coach = new Coaches();
        $coach->setRoles(['ROLE_TEST']);

        $coachRepo = $this->getMockBuilder(Coaches::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $coachRepo->method('findOneBy')->willReturn($coach);

        $this->objectManager->method('getRepository')->willReturn($coachRepo);
        $this->objectManager->expects($this->once())->method('flush');

        $request = [
            'name' => 'Roles',
            'value'=> 'USER',
            'pk' => '1'
        ];

        $this->adminService->traiteModification($request, Coaches::class);
        $this->assertEquals(['role' => 'ROLE_USER'], $coach->getRoles());
    }

    /**
     * @test
     */
    public function les_infos_sont_bien_traitees_pour_Matches()
    {
        $match = new Matches();
        $match->setFans(25);

        $matchRepo = $this->getMockBuilder(Matches::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $matchRepo->method('findOneBy')->willReturn($match);

        $this->objectManager->method('getRepository')->willReturn($matchRepo);
        $this->objectManager->expects($this->once())->method('flush');

        $request = [
            'name' => 'Fans',
            'value'=> '52',
            'pk' => '1'
        ];

        $this->adminService->traiteModification($request, Matches::class);
        $this->assertEquals(52, $match->getFans());
    }

    /**
     * @test
     */
    public function les_infos_sont_bien_traitees_pour_Players()
    {
        $player = new Players();
        $player->setName('Pipou');

        $playerRepo = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $playerRepo->method('findOneBy')->willReturn($player);

        $this->objectManager->method('getRepository')->willReturn($playerRepo);
        $this->objectManager->expects($this->once())->method('flush');

        $request = [
            'name' => 'Name',
            'value'=> 'jean',
            'pk' => '1'
        ];

        $this->adminService->traiteModification($request, Players::class);
        $this->assertEquals('jean', $player->getName());
    }

    /**
     * @test
     */
    public function les_infos_sont_bien_traitees_pour_Team()
    {
        $team = new Teams();
        $team->setName('Pipou Team');

        $teamRepo = $this->getMockBuilder(Teams::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $teamRepo->method('findOneBy')->willReturn($team);

        $this->objectManager->method('getRepository')->willReturn($teamRepo);
        $this->objectManager->expects($this->once())->method('flush');

        $request = [
            'name' => 'Name',
            'value'=> 'jean Team',
            'pk' => '1'
        ];

        $this->adminService->traiteModification($request, Teams::class);
        $this->assertEquals('jean Team', $team->getName());
    }


    /**
     * @test
     */
    public function les_infos_sont_bien_traitees_par_defaut()
    {
        $stade = new Stades();
        $stade->setNom('Pipou Stade');

        $stadeRepo = $this->getMockBuilder(Stades::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $stadeRepo->method('findOneBy')->willReturn($stade);

        $this->objectManager->method('getRepository')->willReturn($stadeRepo);
        $this->objectManager->expects($this->once())->method('flush');

        $request = [
            'name' => 'Nom',
            'value'=> 'jean Stade',
            'pk' => '1'
        ];

        $this->adminService->traiteModification($request, Stades::class);
        $this->assertEquals('jean Stade', $stade->getNom());
    }
}