<?php


namespace App\Service;


use App\Entity\Matches;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class ConfrontationService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    /**
     * @var SettingsService
     */
    private SettingsService $settingService;

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        SettingsService $settingsService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->settingService = $settingsService;
    }


    /**
     * @param $anneeCourante
     * @return array
     */
    public function generateTeamTable($anneeCourante): array
    {

        $equipesAnneCourrante = $this->doctrineEntityManager->getRepository(Teams::class)->findBy(['year' => $anneeCourante], ['name' => 'ASC']);

        $tableau = [];

        /** @var Teams $team1 */
        foreach ($equipesAnneCourrante as $team1) {
            /** @var Teams $team2 */
            foreach ($equipesAnneCourrante as $team2) {
                $tableau[$team1->getName()][$team2->getName()] = 0;
            }
        }

        return $tableau;
    }

    /**
     * @param $tableTeam
     * @param $anneeCourante
     * @return array|mixed
     */
    public function fillTeamTable($tableTeam, $anneeCourante) {

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->tousLesMatchDuneAnne($anneeCourante);

        /** @var Matches $match */
        foreach ($matches as $match) {

            $tableTeam = $this->addMatch($tableTeam, $match->getTeam1()->getName(), $match->getTeam2()->getName());
            $tableTeam = $this->addMatch($tableTeam, $match->getTeam2()->getName(), $match->getTeam1()->getName());

        }

        return $tableTeam;

    }

    /**
     * @param array $tableTeam
     * @param $teamName1
     * @param $teamName2
     * @return array
     */
    private function addMatch($tableTeam, $teamName1, $teamName2): array
    {
        if (!isset($tableTeam[$teamName1][$teamName2])) {
            $tableTeam[$teamName1][$teamName2] = 0;
        }

        $total = $tableTeam[$teamName1][$teamName2] ?? 1;
        $total += 1;
        $tableTeam[$teamName1][$teamName2] = $total;

        return $tableTeam;
    }

    public function generateConfrontationTable() {

        $currentYear = $this->settingService->anneeCourante();

        $tableTeam = $this->generateTeamTable($currentYear);

        return $this->fillTeamTable($tableTeam, $currentYear);
    }
}