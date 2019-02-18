<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Players;

/**
 * Matches
 *
 * @ORM\Table(name="matches", indexes={@ORM\Index(name="idx_team1_id_team2_id", columns={"team1_id", "team2_id"}),
 *     @ORM\Index(name="idx_team2_id", columns={"team2_id"}),
 *     @ORM\Index(name="IDX_62615BAE72BCFA4", columns={"team1_id"})})
 * @ORM\Entity
 */
class Matches
{
    /**
     * @var int
     *
     * @ORM\Column(name="match_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $matchId;

    /**
     * @var int
     *
     * @ORM\Column(name="fans", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $fans = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ffactor1", type="integer", nullable=true)
     */
    private $ffactor1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ffactor2", type="integer", nullable=true)
     */
    private $ffactor2;

    /**
     * @var int|null
     *
     * @ORM\Column(name="income1", type="integer", nullable=true)
     */
    private $income1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="income2", type="integer", nullable=true)
     */
    private $income2;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="team1_score", type="integer", nullable=true)
     */
    private $team1Score;

    /**
     * @var int|null
     *
     * @ORM\Column(name="team2_score", type="integer", nullable=true)
     */
    private $team2Score;

    /**
     * @var int
     *
     * @ORM\Column(name="tv1", type="integer", nullable=false)
     */
    private $tv1 = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="tv2", type="integer", nullable=false)
     */
    private $tv2 = 0;

    /**
     * @var Teams
     *
     * @ORM\ManyToOne(targetEntity="Teams", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team1_id", referencedColumnName="team_id")
     * })
     */
    private $team1;

    /**
     * @var Teams
     *
     * @ORM\ManyToOne(targetEntity="Teams", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team2_id", referencedColumnName="team_id")
     * })
     */
    private $team2;

    public function getMatchId(): ?int
    {
        return $this->matchId;
    }

    public function getFans(): ?int
    {
        return $this->fans;
    }

    public function setFans(int $fans): self
    {
        $this->fans = $fans;

        return $this;
    }

    public function getFfactor1(): ?int
    {
        return $this->ffactor1;
    }

    public function setFfactor1(?int $ffactor1): self
    {
        $this->ffactor1 = $ffactor1;

        return $this;
    }

    public function getFfactor2(): ?int
    {
        return $this->ffactor2;
    }

    public function setFfactor2(?int $ffactor2): self
    {
        $this->ffactor2 = $ffactor2;

        return $this;
    }

    public function getIncome1(): ?int
    {
        return $this->income1;
    }

    public function setIncome1(?int $income1): self
    {
        $this->income1 = $income1;

        return $this;
    }

    public function getIncome2(): ?int
    {
        return $this->income2;
    }

    public function setIncome2(?int $income2): self
    {
        $this->income2 = $income2;

        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getTeam1Score(): ?int
    {
        return $this->team1Score;
    }

    public function setTeam1Score(?int $team1Score): self
    {
        $this->team1Score = $team1Score;

        return $this;
    }

    public function getTeam2Score(): ?int
    {
        return $this->team2Score;
    }

    public function setTeam2Score(int $team2Score): self
    {
        $this->team2Score = $team2Score;

        return $this;
    }

    public function getTv1(): ?int
    {
        return $this->tv1;
    }

    public function setTv1(int $tv1): self
    {
        $this->tv1 = $tv1;

        return $this;
    }

    public function getTv2(): ?int
    {
        return $this->tv2;
    }

    public function setTv2(int $tv2): self
    {
        $this->tv2 = $tv2;

        return $this;
    }

    public function getTeam1(): ?Teams
    {
        return $this->team1;
    }

    public function setTeam1(Teams $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?Teams
    {
        return $this->team2;
    }

    public function setTeam2(Teams $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }
}
