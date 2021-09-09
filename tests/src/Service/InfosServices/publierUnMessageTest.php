<?php


namespace App\Tests\src\Service\InfosServices;


use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class publierUnMessageTest extends TestCase
{
    /**
     * @test
     */
    public function un_message_est_bien_enregistre_pour_creation_d_equipe()
    {
        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class),
            $envMock
        );

        $this->assertIsObject($infosServiceTest->publierUnMessage('test'));
    }
}