<?php

namespace App\Tests\src\Factory\TeamsFactory;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Factory\TeamsFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class lancerEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function une_equipe_est_cree_bb2016(): void
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
                $this->createMock(Coaches::class),
                RulesetEnum::BB_2016
            )
        );
    }

    /**
     * @test
     */
    public function une_equipe_est_cree_bb2020(): void
    {
        $this->assertInstanceOf(
            Teams::class,
            TeamsFactory::lancerEquipe(
                1_000_000,
                'test team',
                150,
                $this->createMock(Stades::class),
                4,
                $this->createMock(RacesBb2020::class),
                $this->createMock(Coaches::class),
                RulesetEnum::BB_2020
            )
        );
    }
}