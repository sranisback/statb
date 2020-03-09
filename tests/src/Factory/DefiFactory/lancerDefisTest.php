<?php

namespace App\Tests\src\Factory\DefiFactory;


use App\Entity\Defis;
use App\Entity\Teams;
use App\Factory\DefiFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class lancerDefisTest extends KernelTestCase
{
    /**
     * @test
     */
    public function lancer_un_defi(): void
    {
        $defiFactoryTest = new DefiFactory();

        $teamMock0 = $this->createMock(Teams::class);
        $teamMock1 = $this->createMock(Teams::class);

        $this->assertInstanceOf(Defis::class, $defiFactoryTest->lancerDefis($teamMock0, $teamMock1));
    }
}