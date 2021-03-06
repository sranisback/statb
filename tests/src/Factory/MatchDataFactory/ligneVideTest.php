<?php
namespace App\Tests\src\Factory\MatchDataFactory;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Factory\MatchDataFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ligneVideTest extends KernelTestCase
{
    /**
     * @test
     */
    public function creation_ligne_vide(): void
    {
        $this->assertInstanceOf(MatchData::class, MatchDataFactory::ligneVide(
            $this->createMock(Players::class),
            $this->createMock(Matches::class)
        ));
    }
}