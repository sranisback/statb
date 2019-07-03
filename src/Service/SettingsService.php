<?php

namespace App\Service;

use App\Entity\Citations;
use App\Entity\Dyk;
use App\Entity\Setting;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class SettingsService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @return int $anneeCourante
     */
    public function anneeCourante()
    {
        $anneeCourante = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        if (!empty($anneeCourante)) {
            return $anneeCourante->getValue();
        }

        return 0;
    }

    /**
     * @return Citations|\App\Entity\Dyk|\App\Entity\GameDataSkills|\App\Entity\Matches
     */
    public function tirerCitationAuHasard()
    {
        $citations = $this->doctrineEntityManager->getRepository(Citations::class)->findAll();

        $nbrAuHasard = rand(1, count($citations) - 1);

        return $citations[$nbrAuHasard];
    }

    /**
     * @return string
     */
    public function tirerDYKauHasard()
    {
        $dyk = $this->doctrineEntityManager->getRepository(Dyk::class)->findAll();

        $nbrAuHasard = rand(1, count($dyk) - 1);

        return '<b>Did you know ?</b> <i>'.$dyk[$nbrAuHasard]->getDykText().'</i>';
    }

    public function periodeDefisCourrante()
    {
        $setting = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'periodeDefis']);

        return [
            'debut' => DateTime::createFromFormat("m/d/Y", $setting->getValue()),
            'fin' => DateTime::createFromFormat(
                "d/m/Y",
                date('d/m/Y', (int)strtotime($setting->getValue().'+2 months'))
            ),
        ];
    }

    public function dateDansLaPeriodeCourante($date)
    {
        $periodeCourrante = $this->periodeDefisCourrante();

        if (($date > $periodeCourrante['debut']) && ($date < $periodeCourrante['fin'])) {
            return true;
        }
        return false;
    }

    public function mettreaJourLaPeriode($maintenant)
    {
        $periode  = $this->periodeDefisCourrante();

        if (DateTime::createFromFormat("m/d/Y", $maintenant) > $periode['fin']) {
            $setting = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(
                ['name' => 'periodeDefis']
            );
            $setting->setValue(date('m/d/Y', (int)strtotime($setting->getValue().'+2 months')));

            $this->doctrineEntityManager->persist($setting);
            $this->doctrineEntityManager->flush();

            return true;
        }
        return false;
    }
}
