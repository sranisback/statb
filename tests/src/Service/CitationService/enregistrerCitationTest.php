<?php

namespace App\Tests\src\Service\CitationService;


use App\Entity\Citations;
use App\Entity\Coaches;
use App\Service\CitationService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class enregistrerCitationTest extends KernelTestCase
{
    /**
     * @test
     */
    public function une_citation_est_ajoutee()
    {
        $coachMock = $this->createMock(Coaches::class);

        $datas = ['coachId' => 0, 'citation' => 'monde de merde'];

        $coachRepoMock = $this->createMock(ObjectRepository::class);
        $coachRepoMock->method('findOneBy')->willReturn($coachMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($coachRepoMock) {
                if ($entityName === 'App\Entity\Coaches') {
                    return $coachRepoMock;
                }
                return true;
            }
        ));

        $citationService = new CitationService($objectManager);

        $this->assertNull($citationService->enregistrerCitation($datas));
    }
}