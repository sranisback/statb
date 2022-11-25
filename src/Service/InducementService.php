<?php


namespace App\Service;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use Doctrine\ORM\EntityManagerInterface;

class InducementService
{
    private EntityManagerInterface $doctrineEntityManager;

    private const POP = 10_000;
    private const ASSISTANT_COACH = 10_000;
    private const CHEERLEADER = 10_000;
    private const APOTHICAIRE = 50_000;
    private const PAYEMENT_STADE = 70_000;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @return array<string,int|null>
     */
    public function ajoutInducement(Teams $equipe, string $type, EquipeGestionService $equipeGestionService): array
    {
        $nbr = 0;
        $inducost = 0;

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                list($nbr, $inducost) = $this->achatRR($equipe, $matches);
                break;
            case "pop":
                list($nbr, $inducost) = $this->achatFanFactor($equipe, $matches);
                break;
            case "ac":
                list($nbr, $inducost) = $this->achatAssistantCoach($equipe);
                break;
            case "chl":
                list($nbr, $inducost) = $this->achatCheerleaders($equipe);
                break;
            case "apo":
                list($nbr, $inducost) = $this->achatApo($equipe);
                break;
            case "pay":
                list($nbr, $inducost) = $this->payeStade($equipe);
                break;
            default:
                break;
        }
        $equipe->setTreasury($equipe->getTreasury() - $inducost);
        $equipe->setTv($equipeGestionService->tvDelEquipe($equipe));

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        return ['inducost' => $inducost, 'nbr' => $nbr];
    }


    /**
     * @param Teams $equipe
     * @param $matches
     * @return array
     */
    private function achatRR(Teams $equipe, $matches): array
    {
        $inducost = 0;
        $nbr = 0;
        $race = RulesetEnum::getRaceFromEquipeByRuleset($equipe);

        if ($race !== null) {
            $coutRR = $race->getCostRr();
            $nbr = $equipe->getRerolls();

            if (count($matches) > 0) {
                $coutRR *= 2;
            }
            if ($equipe->getTreasury() >= $coutRR) {
                $inducost = $coutRR;
                $nbr += 1;
                $equipe->setRerolls($nbr);
            }
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @param $matches
     * @return array
     */
    private function achatFanFactor(Teams $equipe, $matches): array
    {
        $inducost = 0;

        $nbr = $equipe->getFfBought() + $equipe->getFf();

        if (count($matches) === 0 && $equipe->getTreasury() >= self::POP) {
            $nbr += 1;
            $equipe->setFfBought($equipe->getFfBought() + 1);
            $inducost = self::POP;
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function achatAssistantCoach(Teams $equipe): array
    {
        $inducost = 0;

        $nbr = $equipe->getAssCoaches();

        if ($equipe->getTreasury() >= self::ASSISTANT_COACH) {
            $nbr += 1;
            $equipe->setAssCoaches($nbr);
            $inducost = self::ASSISTANT_COACH;
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function achatCheerleaders(Teams $equipe): array
    {
        $inducost = 0;

        $nbr = $equipe->getCheerleaders();

        if ($equipe->getTreasury() >= self::CHEERLEADER) {
            $nbr += 1;
            $equipe->setCheerleaders($nbr);
            $inducost = self::CHEERLEADER;
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function achatApo(Teams $equipe): array
    {
        $inducost = 0;

        $equipe->getApothecary() === 1 ? $nbr = 1 : $nbr = 0;
        if ($equipe->getTreasury() >= self::APOTHICAIRE && $equipe->getApothecary() === 0) {
            $nbr = 1;
            $equipe->setApothecary(1);
            $inducost = self::APOTHICAIRE;
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function payeStade(Teams $equipe): array
    {
        $inducost = 0;

        $stadeDelEquipe = $equipe->getFStades();

        $nbr = $stadeDelEquipe->getTotalPayement();

        if ($equipe->getTreasury() >= self::PAYEMENT_STADE) {
            $nbr += 50_000;
            $stadeDelEquipe->setTotalPayement($nbr);
            $this->doctrineEntityManager->persist($stadeDelEquipe);
            $inducost = 70_000;
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @param string $type
     * @return array<string,int|null>
     */
    public function supprInducement(Teams $equipe, string $type, EquipeGestionService $equipeGestionService): array
    {
        $nbr = 0;
        $inducost = 0;

        $matches = $this->doctrineEntityManager->getRepository(Matches::class)->listeDesMatchs($equipe);

        switch ($type) {
            case "rr":
                list($inducost, $nbr) = $this->supprRerol($equipe);
                break;
            case "pop":
                list($nbr, $inducost) = $this->supprFanFactor($equipe, $matches);
                break;
            case "ac":
                list($nbr, $inducost) = $this->supprAssistantCoach($equipe);
                break;
            case "chl":
                list($nbr, $inducost) = $this->supprCheerleader($equipe);
                break;
            case "apo":
                list($nbr, $inducost) = $this->supprApothicaire($equipe);
                break;
            case "pay":
                list($nbr, $inducost) = $this->supprPaiementStade($equipe);
                break;
            default:
                break;
        }

        if (count($matches) === 0) {
            $nouveauTresor = $equipe->getTreasury() + $inducost;
            $equipe->setTreasury($nouveauTresor);
        }

        $equipe->setTv($equipeGestionService->tvDelEquipe($equipe));

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        return ['inducost' => $inducost, 'nbr' => $nbr];
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function supprRerol(Teams $equipe): array
    {
        $nbr = 0;
        $inducost = 0;

        $race = RulesetEnum::getRaceFromEquipeByRuleset($equipe);

        if ($race !== null && $equipe->getRerolls() > 0) {
            $inducost = $race->getCostRr();
            $nbr = $equipe->getRerolls() - 1;
            $equipe->setRerolls($nbr);
        }
        return array($inducost, $nbr);
    }

    /**
     * @param Teams $equipe
     * @param $matches
     * @return array
     */
    private function supprFanFactor(Teams $equipe, $matches): array
    {
        $inducost = 0;

        $nbr = $equipe->getFfBought() + $equipe->getFf();
        if (count($matches) === 0 && $equipe->getFfBought() > 0) {
            $inducost = self::POP;
            $nbr -= 1;
            $equipe->setFfBought($equipe->getFfBought() - 1);
            if($equipe->getFfBought() === 0 && $equipe->getRuleset() === RulesetEnum::BB_2020) {
                $equipe->setTreasury($equipe->getTreasury() + self::POP);
            }
            if($equipe->getFfBought() < 0 && $equipe->getRuleset() === RulesetEnum::BB_2016) {
                $equipe->setFfBought(0);
                $nbr = 0;
            }
        }
        if (count($matches) === 0 && $equipe->getFfBought() == 0 && $equipe->getFf() > 0) {
            $inducost = self::POP;
            $nbr -= 1;
            $equipe->setFf($equipe->getFf() - 1);
            if($equipe->getFf() === 0 && $equipe->getRuleset() === RulesetEnum::BB_2020) {
                $equipe->setFf(1);
                $nbr = 1;
                $equipe->setTreasury($equipe->getTreasury() - self::POP);
            }
            if($equipe->getFf() < 0 && $equipe->getRuleset() === RulesetEnum::BB_2016) {
                $equipe->setFf(0);
                $nbr = 0;
            }
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function supprAssistantCoach(Teams $equipe): array
    {
        $inducost = 0;

        $nbr = $equipe->getAssCoaches();
        if ($nbr > 0) {
            $inducost = self::ASSISTANT_COACH;
            $nbr -= 1;
            $equipe->setAssCoaches($nbr);
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function supprCheerleader(Teams $equipe): array
    {
        $inducost = 0;

        $nbr = $equipe->getCheerleaders();
        if ($nbr > 0) {
            $inducost = self::CHEERLEADER;
            $nbr -= 1;
            $equipe->setCheerleaders($nbr);
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return int[]
     */
    private function supprApothicaire(Teams $equipe): array
    {
        $inducost = 0;

        $equipe->getApothecary() === 1 ? $nbr = 1 : $nbr = 0;
        if ($equipe->getApothecary() === 1) {
            $inducost = self::APOTHICAIRE;
            $nbr = 0;
            $equipe->setApothecary(0);
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    private function supprPaiementStade(Teams $equipe): array
    {
        $inducost = 0;

        $stadeDelEquipe = $equipe->getFStades();
        $nbr = $stadeDelEquipe->getTotalPayement();
        if ($nbr > 0) {
            $nbr -= 50_000;
            $stadeDelEquipe->setTotalPayement($nbr);
            $inducost = self::PAYEMENT_STADE;
        }
        return array($nbr, $inducost);
    }

    /**
     * @param Teams $equipe
     * @return array<string,mixed>
     */
    public function valeurInducementDelEquipe(Teams $equipe): array
    {
        $equipeRace = RulesetEnum::getRaceFromEquipeByRuleset($equipe);

        if ($equipeRace !== null) {
            $inducement['rerolls'] = $equipe->getRerolls() * $equipeRace->getCostRr();
        }

        $totalPop = $equipe->getFf() + $equipe->getFfBought();
        if($equipe->getRuleset() == RulesetEnum::BB_2020) {
            $inducement['pop'] = 0;
        } else {
            $inducement['pop'] = $totalPop * 10_000;
        }

        $inducement['asscoaches'] = $equipe->getAssCoaches() * 10_000;
        $inducement['cheerleader'] = $equipe->getCheerleaders() * 10_000;
        $inducement['apo'] = $equipe->getApothecary() * 50_000;
        $inducement['total'] = $inducement['rerolls'] + $inducement['pop']
            + $inducement['asscoaches'] + $inducement['cheerleader'] + $inducement['apo'];

        return $inducement;
    }
}