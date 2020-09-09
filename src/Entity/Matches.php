<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Matches
 *
 * @ORM\Table(name="matches", indexes={@ORM\Index(name="idx_team1_id_team2_id", columns={"team1_id", "team2_id"}),
 *     @ORM\Index(name="idx_team2_id", columns={"team2_id"}),
 *     @ORM\Index(name="IDX_62615BAE72BCFA4", columns={"team1_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\MatchesRepository")
 */
class Matches
{
    /**
     *
     * @ORM\Column(name="match_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private int $matchId;

    /**
     *
     * @ORM\Column(name="fans", type="integer", nullable=false, options={"unsigned"=true})
     * @var int
     */
    private int $fans = 0;

    /**
     *
     * @ORM\Column(name="ffactor1", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $ffactor1 = 0;

    /**
     *
     * @ORM\Column(name="ffactor2", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $ffactor2 = 0;

    /**
     *
     * @ORM\Column(name="income1", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $income1 = 0;

    /**
     *
     * @ORM\Column(name="income2", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $income2 = 0;

    /**
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $dateCreated = null;

    /**
     *
     * @ORM\Column(name="team1_score", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $team1Score = 0;

    /**
     *
     * @ORM\Column(name="team2_score", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $team2Score = 0;

    /**
     *
     * @ORM\Column(name="tv1", type="integer", nullable=false)
     * @var int
     */
    private int $tv1 = 0;

    /**
     *
     * @ORM\Column(name="tv2", type="integer", nullable=false)
     * @var int
     */
    private int $tv2 = 0;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Teams", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team1_id", referencedColumnName="team_id")
     * })
     * @var \App\Entity\Teams|null
     */
    private ?\App\Entity\Teams $team1 = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Teams", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team2_id", referencedColumnName="team_id")
     * })
     * @var null|\App\Entity\Teams
     */
    private ?\App\Entity\Teams $team2 = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Meteo")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?\App\Entity\Meteo $fMeteo = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameDataStadium", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?\App\Entity\GameDataStadium $fStade = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueBlessure", mappedBy="fmatch", fetch="EAGER")
     * @var \App\Entity\HistoriqueBlessure[]|\Doctrine\Common\Collections\Collection
     */
    private \Doctrine\Common\Collections\Collection $blessuresMatch;
/**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private ?int $stadeAcceuil = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $depense1 = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $depense2 = 0;

    public function __construct()
    {
        $this->blessuresMatch = new ArrayCollection();
    }

    public function getMatchId(): int
    {
        return $this->matchId;
    }

    public function getFans(): int
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

    public function getDateCreated(): ?\DateTimeInterface
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

    public function getTv1(): int
    {
        return $this->tv1;
    }

    public function setTv1(int $tv1): self
    {
        $this->tv1 = $tv1;

        return $this;
    }

    public function getTv2(): int
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

    public function getFMeteo(): ?\App\Entity\Meteo
    {
        return $this->fMeteo;
    }

    public function setFMeteo(?Meteo $fMeteo): self
    {
        $this->fMeteo = $fMeteo;

        return $this;
    }

    public function getFStade(): ?\App\Entity\GameDataStadium
    {
        return $this->fStade;
    }

    public function setFStade(?GameDataStadium $fStade): self
    {
        $this->fStade = $fStade;

        return $this;
    }

    /**
     * @return Collection|HistoriqueBlessure[]
     */
    public function getBlessuresMatch(): \Doctrine\Common\Collections\Collection
    {
        return $this->blessuresMatch;
    }

    public function addBlessuresMatch(HistoriqueBlessure $blessuresMatch): self
    {
        if (!$this->blessuresMatch->contains($blessuresMatch)) {
            $this->blessuresMatch[] = $blessuresMatch;
            $blessuresMatch->setFmatch($this);
        }

        return $this;
    }

    public function removeBlessuresMatch(HistoriqueBlessure $blessuresMatch): self
    {
        if ($this->blessuresMatch->contains($blessuresMatch)) {
            $this->blessuresMatch->removeElement($blessuresMatch);
            // set the owning side to null (unless already changed)
            if ($blessuresMatch->getFmatch() === $this) {
                $blessuresMatch->setFmatch(null);
            }
        }

        return $this;
    }

    public function getStadeAcceuil(): ?int
    {
        return $this->stadeAcceuil;
    }

    public function setStadeAcceuil(?int $stadeAcceuil): self
    {
        $this->stadeAcceuil = $stadeAcceuil;
        return $this;
    }

    public function getDepense1(): ?int
    {
        return $this->depense1;
    }

    public function setDepense1(?int $depense1): self
    {
        $this->depense1 = $depense1;

        return $this;
    }

    public function getDepense2(): ?int
    {
        return $this->depense2;
    }

    public function setDepense2(?int $depense2): self
    {
        $this->depense2 = $depense2;

        return $this;
    }
}
