<?php

namespace App\Entity;

use App\Repository\ScoreCalculRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoreCalculRepository::class)
 */
class ScoreCalcul
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Teams::class)
     * @ORM\JoinColumn (name="teamId", referencedColumnName="team_id")
     */
    private $teams;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $bonus;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lostPoint;

    /**
     * @ORM\ManyToOne(targetEntity=Matches::class)
     * @ORM\JoinColumn (name="matchId", referencedColumnName="match_id")
     */
    private $matchCible;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeams(): ?Teams
    {
        return $this->teams;
    }

    public function setTeams(?Teams $teams): self
    {
        $this->teams = $teams;

        return $this;
    }

    public function getBonus(): ?float
    {
        return $this->bonus;
    }

    public function setBonus(?float $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getLostPoint(): ?float
    {
        return $this->lostPoint;
    }

    public function setLostPoint(?float $lostPoint): self
    {
        $this->lostPoint = $lostPoint;

        return $this;
    }

    public function getMatchCible(): ?Matches
    {
        return $this->matchCible;
    }

    public function setMatchCible(Matches $matchCible): self
    {
        $this->matchCible = $matchCible;

        return $this;
    }
}
