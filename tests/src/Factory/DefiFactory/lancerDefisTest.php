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
        $teamMock0 = $this->createMock(Teams::class);
        $teamMock1 = $this->createMock(Teams::class);

        $this->assertInstanceOf(Defis::class, DefiFactory::lancerDefis($teamMock0, $teamMock1));
    }
}