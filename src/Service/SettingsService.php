<?php

namespace App\Service;

use App\Entity\Citations;
use App\Entity\Dyk;
use App\Entity\Setting;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class SettingsService
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private \Doctrine\ORM\EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @return int|string|null $anneeCourante
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
     * @return mixed[]
     */
    public function tirerCitationAuHasard(): array
    {
        $citations = $this->doctrineEntityManager->getRepository(Citations::class)->findAll();

        $nbrAuHasard = rand(1, count($citations) - 1);

        return $citations[$nbrAuHasard];
    }

    /**
     * @return string
     */
    public function tirerDYKauHasard(): string
    {
        $dyk = $this->doctrineEntityManager->getRepository(Dyk::class)->findAll();

        $nbrAuHasard = rand(1, count($dyk) - 1);

        return '<b>Did you know ?</b> <i>'.$dyk[$nbrAuHasard]->getDykText().'</i>';
    }

    /**
     * @return \DateTime[]|bool[]
     */
    public function periodeDefisCourrante(): array
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

    public function dateDansLaPeriodeCourante($date): bool
    {
        $periodeCourrante = $this->periodeDefisCourrante();
        return ($date > $periodeCourrante['debut']) && ($date < $periodeCourrante['fin']);
    }

    public function mettreaJourLaPeriode($maintenant): bool
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

    /**
     * @return int|string|null
     */
    public function recupererTresorDepart()
    {
        $tresorDepart = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(
            ['name' => 'departTresor']
        );

        if (!empty($tresorDepart)) {
            return $tresorDepart->getValue();
        }

        return 0;
    }
}
