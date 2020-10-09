<?php


namespace App\Tests\src\Service\InfosServices;


use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class publierUnMessageTest extends TestCase
{
    /**
     * @test
     */
    public function un_message_est_bien_enregistre_pour_creation_d_equipe()
    {
        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $this->assertIsObject($infosServiceTest->publierUnMessage('test'));
    }
}