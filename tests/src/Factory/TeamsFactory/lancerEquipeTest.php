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
    public function une_equipe_est_cree(): void
    {
        $this->assertInstanceOf(
            Teams::class,
            TeamsFactory::lancerEquipe(
                1_000_000,
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