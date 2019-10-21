<?php

namespace App\Tests\src\Factory\TeamsFactory;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Factory\TeamsFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class lancerEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function une_equipe_est_cree()
    {
        $equipeFactory = new TeamsFactory();

        $this->assertInstanceOf(
            Teams::class,
            $equipeFactory->lancerEquipe(
                1000000,
                'test team',
                150,
                $this->createMock(Stades::class),
                4,
                $this->createMock(Races::class),
                $this->createMock(Coaches::class)
            )
        );
    }

}