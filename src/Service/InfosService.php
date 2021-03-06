<?php


namespace App\Service;

use App\Entity\Defis;
use App\Entity\Infos;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Primes;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class InfosService
{
    /**
     * @var EntityManagerInterface
     */
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
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
            ' a crée l\'équipe <a href="/team/' . $equipe->getTeamId() .
            '">' . $equipe->getName() . '</a>' .
            '(' . $equipe->getFRace()->getName() .
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
            $joueur->getName() . ', ' . $joueur->getFPos()->getPos() . ' ' .$joueur->getFRid()->getName() .
            ' a été engagé par <a href="/team/' . $joueur->getOwnedByTeam()->getTeamId() . '">' .
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
            '<a href="/match/' . $matches->getMatchId() . '">voir</a>'
        );
    }

    /**
     * @param Players $joueur
     * @return Infos
     */
    public function mortDunJoueur(Players $joueur)
    {
        return $this->publierUnMessage(
            $joueur->getName() . ', ' . $joueur->getFPos()->getPos() . ' '  . $joueur->getFRid()->getName() .
            ' de <a href="/team/' . $joueur->getOwnedByTeam()->getTeamId() . '">' .
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
            '<a href="/team/' . $defis->getEquipeOrigine()->getTeamId() . '">' .
            $defis->getEquipeOrigine()->getName() . '</a> (' . $defis->getEquipeOrigine()->getFRace()->getName() .
            ') défie <a href="/team/' . $defis->getEquipeDefiee()->getTeamId() . '">' .
            $defis->getEquipeDefiee()->getName() . '</a> (' . $defis->getEquipeDefiee()->getFRace()->getName() . ')'
        );
    }

    /**
     * @param Defis $defis
     * @return Infos
     */
    public function defisRealise(Defis $defis)
    {
        return $this->publierUnMessage(
            'Le défis ' . '<a href="/team/' . $defis->getEquipeOrigine()->getTeamId() . '">' .
            $defis->getEquipeOrigine()->getName() . '</a> contre ' . '<a href="/team/' .
            $defis->getEquipeDefiee()->getTeamId() . '">' . $defis->getEquipeDefiee()->getName() .
            '</a> a été réalisé : <a href="/match/' . $defis->getMatchDefi()->getMatchId()  . '">Voir</a>'
        );
    }

    /**
     * @param Primes $prime
     * @return Infos
     */
    public function primeMise(Primes $prime)
    {
        return $this->publierUnMessage(
            $prime->getPlayers()->getName() . ', ' . $prime->getPlayers()->getFPos()->getPos() . ' ' .
            $prime->getPlayers()->getFRid()->getName() . ' de ' . $prime->getPlayers()->getOwnedByTeam()->getName() .
            ' a une prime de ' . $prime->getMontant() . ' Po'
        );
    }

    public function primeGagnee(Primes $prime)
    {
        return $this->publierUnMessage(
            $prime->getEquipePrime()->getName() . ' a touché la prime de ' . $prime->getMontant() . 'Po sur ' .
            $prime->getPlayers()->getName()  . ', ' . $prime->getPlayers()->getFPos()->getPos() . ' ' .
            $prime->getPlayers()->getFRid()->getName()
        );
    }
}
