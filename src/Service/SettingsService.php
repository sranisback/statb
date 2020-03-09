<?php

namespace App\Service;

use App\Entity\Citations;
use App\Entity\Dyk;
use App\Entity\Setting;

use Cassandra\Date;
use DateTime;
use DateTimeInterface;
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
     * @return int $anneeCourante
     */
    public function anneeCourante(): int
    {
        $anneeCourante = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        if (!empty($anneeCourante)) {
            return (int) $anneeCourante->getValue();
        }

        return 0;
    }

    /**
     * @return string
     */
    public function tirerCitationAuHasard():string
    {
        $citations = $this->doctrineEntityManager->getRepository(Citations::class)->findAll();

        $nbrAuHasard = rand(1, count($citations) - 1);

        return  $citations[$nbrAuHasard]->getCitation();
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
     * @return array<string, DateTime|false>
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

    /**
     * @param  DateTimeInterface|null $date
     * @return bool
     */
    public function dateDansLaPeriodeCourante(?DateTimeInterface $date): bool
    {
        $periodeCourrante = $this->periodeDefisCourrante();
        return ($date > $periodeCourrante['debut']) && ($date < $periodeCourrante['fin']);
    }

    /**
     * @param string $maintenant
     * @return bool
     */
    public function mettreaJourLaPeriode(string $maintenant): bool
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
     * @return int
     */
    public function recupererTresorDepart()
    {
        $tresorDepart = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(
            ['name' => 'departTresor']
        );

        if (!empty($tresorDepart)) {
            return (int)$tresorDepart->getValue();
        }

        return (int)0;
    }
}
