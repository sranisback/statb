<?php


namespace App\Service;

use App\Entity\Defis;
use App\Entity\Infos;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Primes;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class InfosService
{
    /**
     * @var EntityManagerInterface
     */
    private $doctrineEntityManager;
    
    private $urlPrefix;

    public function __construct(EntityManagerInterface $doctrineEntityManager, ContainerBagInterface $params)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->urlPrefix = $params->get('env') == 'dev' ? '' : '/statb/public';
    }

    /**
     * @param $text
     * @return Infos
     */
    public function publierUnMessage($text)
    {
        $message = new Infos();
        $message->setMessages($text);
        $message->setDate(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

        $this->doctrineEntityManager->persist($message);
        $this->doctrineEntityManager->flush();

        return $message;
    }
    
    /**
     * @param Teams $equipe
     * @return Infos
     */
    public function equipeEstCree(Teams $equipe)
    {
        return $this->publierUnMessage(
            $equipe->getOwnedByCoach()->getName() .
            ' a crée l\'équipe <a href="' . $this->urlPrefix . '/team/' . $equipe->getTeamId() .
            '">' . $equipe->getName() . '</a>' .
            '(' . RulesetEnum::getRaceFromEquipeByRuleset($equipe)->getName() .
            ')'
        );
    }

    /**
     * @param Players $joueur
     * @return Infos
     */
    public function joueurEngage(Players $joueur)
    {
        return $this->publierUnMessage(
            $joueur->getName() . ', ' . RulesetEnum::getPositionFromPlayerByRuleset($joueur)->getPos() . ' ' .
            RulesetEnum::getRaceFromJoueurByRuleset($joueur)->getName() .
            ' a été engagé par <a href="' . $this->urlPrefix . '/team/' . $joueur->getOwnedByTeam()->getTeamId() . '">' .
            $joueur->getOwnedByTeam()->getName() . '</a>' .
            ' de ' . $joueur->getOwnedByTeam()->getOwnedByCoach()->getName()
        );
    }

    /**
     * @param Matches $matches
     * @return Infos
     */
    public function matchEnregistre(Matches $matches)
    {
        return $this->publierUnMessage(
            'Match(' . $matches->getMatchId() . '): ' .
            $matches->getTeam1()->getName() . ' VS ' . $matches->getTeam2()->getName() . ' enregistré. ' .
            '<a href="' . $this->urlPrefix . '/match/' . $matches->getMatchId() . '">voir</a>'
        );
    }

    /**
     * @param Players $joueur
     * @return Infos
     */
    public function mortDunJoueur(Players $joueur)
    {
        return $this->publierUnMessage(
            $joueur->getName() . ', ' .  RulesetEnum::getPositionFromPlayerByRuleset($joueur)->getPos() . ' '  . RulesetEnum::getRaceFromJoueurByRuleset($joueur)->getName() .
            ' de <a href="' . $this->urlPrefix . '/team/' . $joueur->getOwnedByTeam()->getTeamId() . '">' .
            $joueur->getOwnedByTeam()->getName() . '</a> est mort !'
        );
    }

    /**
     * @param Defis $defis
     * @return Infos
     */
    public function defisEstLance(Defis $defis)
    {
        return $this->publierUnMessage(
            '<a href="' . $this->urlPrefix . '/team/' . $defis->getEquipeOrigine()->getTeamId() . '">' .
            $defis->getEquipeOrigine()->getName() . '</a> (' .  RulesetEnum::getRaceFromEquipeByRuleset($defis->getEquipeOrigine())->getName() .
            ') défie <a href="' . $this->urlPrefix . '/team/' . $defis->getEquipeDefiee()->getTeamId() . '">' .
            $defis->getEquipeDefiee()->getName() . '</a> (' . RulesetEnum::getRaceFromEquipeByRuleset($defis->getEquipeDefiee())->getName() . ')'
        );
    }

    /**
     * @param Defis $defis
     * @return Infos
     */
    public function defisRealise(Defis $defis)
    {
        return $this->publierUnMessage(
            'Le défis ' . '<a href="' . $this->urlPrefix . '/team/' . $defis->getEquipeOrigine()->getTeamId() . '">' .
            $defis->getEquipeOrigine()->getName() . '</a> contre ' . '<a href="' . $this->urlPrefix . '/team/' .
            $defis->getEquipeDefiee()->getTeamId() . '">' . $defis->getEquipeDefiee()->getName() .
            '</a> a été réalisé : <a href="' . $this->urlPrefix . '/match/' . $defis->getMatchDefi()->getMatchId()  . '">Voir</a>'
        );
    }

    /**
     * @param Primes $prime
     * @return Infos
     */
    public function primeMise(Primes $prime)
    {
        return $this->publierUnMessage(
            $prime->getPlayers()->getName() . ', ' . RulesetEnum::getPositionFromPlayerByRuleset($prime->getPlayers())->getPos()  . ' ' .
            RulesetEnum::getRaceFromJoueurByRuleset($prime->getPlayers())->getName() . ' de ' . $prime->getPlayers()->getOwnedByTeam()->getName() .
            ' a une prime de ' . $prime->getMontant() . ' Po'
        );
    }

    public function primeGagnee(Primes $prime)
    {
        return $this->publierUnMessage(
            $prime->getEquipePrime()->getName() . ' a touché la prime de ' . $prime->getMontant() . 'Po sur ' .
            $prime->getPlayers()->getName()  . ', ' . RulesetEnum::getPositionFromPlayerByRuleset($prime->getPlayers())->getPos() . ' ' .
            RulesetEnum::getRaceFromJoueurByRuleset($prime->getPlayers())->getName()
        );
    }
}
