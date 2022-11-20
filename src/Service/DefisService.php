<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Factory\DefiFactory;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class DefisService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    private InfosService $infoService;

    private SettingsService $settingsService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, InfosService $infoService, SettingsService $settingsService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->infoService = $infoService;
        $this->settingsService = $settingsService;
    }

    /**
     * @param Defis $defis
     * @return void
     */
    public function creerDefis(Defis $defis)
    {
        $defis->setDateDefi(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

        $this->doctrineEntityManager->persist($defis);
        $this->doctrineEntityManager->flush();

        $this->infoService->defisEstLance($defis);
    }

    public function defiAutorise(Teams $equipe): bool
    {
        foreach ($this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $equipe->getOwnedByCoach(), 'year' => $this->settingsService->anneeCourante()]
        ) as $equipeVerif) {
            $defis =  $this->doctrineEntityManager->getRepository(Defis::class)->findBy(
                ['equipeOrigine' => $equipeVerif->getTeamId()]
            );
            if (count($defis) > 0) {
                /** @var Defis $defisVerifie */
                foreach ($defis as $defisVerifie) {
                    if ($this->settingsService->dateDansLaPeriodeCourante($defisVerifie->getDateDefi())) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param Defis $defi
     * @return string
     */
    public function supprimerDefis(Defis $defi): string
    {
        $this->doctrineEntityManager->remove($defi);

        $this->doctrineEntityManager->flush();

        return 'ok';
    }

    public function verificationDefis(Matches $matches): ?Defis
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
     * @param Coaches $coach
     * @return array
     */
    public function lesDefisEnCoursContreLeCoach(Coaches $coach): array
    {
        $contenuMessage = [];

        foreach ($this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $coach->getCoachId(), 'year' => $this->settingsService->anneeCourante()]
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

    /**
     * @param $form
     * @param Defis $defis
     * @return int
     */
    public function ajoutDefiForm($form, Defis $defis): int
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->defiAutorise($defis->getEquipeOrigine())) {
                $this->creerDefis($defis);

                return 1;
            }
            return 2;
        }

        return 3;
    }
}
