<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Factory\DefiFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DefisService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    public InfosService $infoService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, InfosService $infoService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->infoService = $infoService;
    }

    /**
     * @param array<string,mixed> $datas
     * @return Defis
     */
    public function creerDefis(array $datas): \App\Entity\Defis
    {
        $defis = (new DefiFactory)->lancerDefis(
            $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
                ['teamId' => $datas['equipeDefiee']]
            ),
            $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
                ['teamId' => $datas['equipeOrigine']]
            )
        );

        $this->doctrineEntityManager->persist($defis);
        $this->doctrineEntityManager->flush();

        $this->infoService->defisEstLance($defis);

        return $defis;
    }

    public function defiAutorise(Teams $equipe, SettingsService $settingsService): bool
    {
        foreach ($this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $equipe->getOwnedByCoach(), 'year' => $settingsService->anneeCourante()]
        ) as $equipeVerif) {
            $defis =  $this->doctrineEntityManager->getRepository(Defis::class)->findBy(
                ['equipeOrigine' => $equipeVerif->getTeamId()]
            );
            if (count($defis) > 0) {
                /** @var Defis $defisVerifie */
                foreach ($defis as $defisVerifie) {
                    if ($settingsService->dateDansLaPeriodeCourante($defisVerifie->getDateDefi())) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param int $defisId
     * @return string
     */
    public function supprimerDefis(int $defisId): string
    {
        $prime = $this->doctrineEntityManager->getRepository(Defis::class)->findOneBy(['id' => $defisId]);

        $this->doctrineEntityManager->remove($prime);

        $this->doctrineEntityManager->flush();

        return 'ok';
    }

    public function verificationDefis(Matches $matches): ?\App\Entity\Defis
    {
        $defiEnCours = null;

        if (!empty($matches->getTeam1()) && !empty($matches->getTeam2())) {
            /** @var Defis $defiEnCours */
            $defiEnCours = $this->doctrineEntityManager->getRepository(Defis::class)->listeDeDefisActifPourLeMatch(
                $matches->getTeam1()->getTeamId(),
                $matches->getTeam2()->getTeamId()
            );
        }

        if (!empty($defiEnCours)) {
            $defiEnCours->setMatchDefi($matches);
            $defiEnCours->setDefieRealise(true);

            $this->doctrineEntityManager->persist($defiEnCours);
            $this->doctrineEntityManager->flush();

            $this->infoService->defisRealise($defiEnCours);
        }

        return $defiEnCours;
    }

    /**
     * @return string[][]|null[][]
     */
    public function lesDefisEnCoursContreLeCoach(SettingsService $settingsService, Coaches $coach): array
    {
        $contenuMessage = [];

        foreach ($this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $coach->getCoachId(), 'year' => $settingsService->anneeCourante()]
        ) as $equipeDuCoach) {
            foreach ($this->doctrineEntityManager->getRepository(Defis::class)->findBy(
                ['equipeDefiee' => $equipeDuCoach, 'defieRealise' => 0]
            ) as $defisEnCours) {
                $contenuMessage[] = [
                    'defiee' => $equipeDuCoach->getName(),
                    'par' => $defisEnCours->getEquipeOrigine()->getName(),
                ];
            }
        }

        return $contenuMessage;
    }
}
