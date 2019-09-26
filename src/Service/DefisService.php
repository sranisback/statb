<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DefisService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function creerDefis($datas)
    {
        $defis = new Defis();
        $dateDefis = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        if ($dateDefis != false) {
            $defis->setDateDefi($dateDefis);
        } else {
            throw new \Exception('problème Datetime Defis');
        }
        $defis->setEquipeDefiee(
            $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
                ['teamId' => $datas['equipeDefiee']]
            )
        );
        $defis->setEquipeOrigine(
            $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
                ['teamId' => $datas['equipeOrigine']]
            )
        );

        $this->doctrineEntityManager->persist($defis);
        $this->doctrineEntityManager->flush();

        return $defis;
    }

    public function defiAutorise(Teams $equipe, SettingsService $settingsService)
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

    public function supprimerDefis($defisId)
    {
        $prime = $this->doctrineEntityManager->getRepository(Defis::class)->findOneBy(['id' => $defisId]);

        $this->doctrineEntityManager->remove($prime);

        $this->doctrineEntityManager->flush();

        return 'ok';
    }

    public function verificationDefis(Matches $matches)
    {
        if (!empty($matches->getTeam1()) && !empty($matches->getTeam2())) {
            /** @var Defis $defiEnCours */
            $defiEnCours = $this->doctrineEntityManager->getRepository(Defis::class)->listeDeDefisActifPourLeMatch(
                $matches->getTeam1()->getTeamId(),
                $matches->getTeam2()->getTeamId()
            );
        } else {
            throw new \Exception('pas d\'équipe dans le match');
        }
        if (!empty($defiEnCours)) {
                $defiEnCours->setMatchDefi($matches);
                $defiEnCours->setDefieRealise(true);

                $this->doctrineEntityManager->persist($defiEnCours);
                $this->doctrineEntityManager->flush();
        }

        return $defiEnCours;
    }

    public function lesDefisEnCoursContreLeCoach(SettingsService $settingsService, Coaches $coach)
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
