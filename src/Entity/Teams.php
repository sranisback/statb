<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Teams
 *
 * @ORM\Table(name="teams", indexes={@ORM\Index(name="idx_owned_by_coach_id", columns={"owned_by_coach_id"}),
 * @ORM\Index(name="fk_teams_races_idx", columns={"f_race_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Teams
{
    /**
     *
     * @ORM\Column(name="team_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private ?int $teamId = 0;

    /**
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     * @var null|string
     */
    private ?string $name = null;

    /**
     *
     * @ORM\Column(name="treasury", type="bigint", nullable=true)
     * @var int|null
     */
    private ?int $treasury = 0;

    /**
     *
     * @ORM\Column(name="apothecary", type="integer", nullable=true)
     * @var int
     */
    private int $apothecary = 0;

    /**
     *
     * @ORM\Column(name="rerolls", type="integer", nullable=true, options={"unsigned"=true})
     * @var int
     */
    private int $rerolls = 0;

    /**
     *
     * @ORM\Column(name="ff_bought", type="integer", nullable=true)
     * @var int
     */
    private int $ffBought = 0;

    /**
     *
     * @ORM\Column(name="ass_coaches", type="integer", nullable=true, options={"unsigned"=true})
     * @var int
     */
    private int $assCoaches = 0;

    /**
     *
     * @ORM\Column(name="cheerleaders", type="integer", nullable=true, options={"unsigned"=true})
     * @var int
     */
    private int $cheerleaders = 0;

    /**
     *
     * @ORM\Column(name="retired", type="boolean", nullable=false)
     * @var bool
     */
    private bool $retired = false;

    /**
     *
     * @ORM\Column(name="ff", type="integer", nullable=true)
     * @var int
     */
    private int $ff = 0;

    /**
     *
     * @ORM\Column(name="elo", type="float", precision=10, scale=0, nullable=true)
     * @var float|null
     */
    private ?float $elo = null;


    /**
     *
     * @ORM\Column(name="tv", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $tv = 0;

    /**
     *
     * @ORM\Column(name="year", type="integer", nullable=false)
     * @var int
     */
    private int $year = 0;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Coaches", inversedBy="equipes")
     *   @ORM\JoinColumn  (name="owned_by_coach_id",  referencedColumnName="coach_id")
     * @var Coaches|null
     */
    private ?Coaches $ownedByCoach = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Races")
     * @ORM\JoinColumn(name="f_race_id", referencedColumnName="race_id", nullable=true)
     * @var Races|null
     */
    private ?Races $fRace = null;

    /**
     *
     * @ORM\OneToOne(targetEntity="Stades", cascade={"remove"})
     * @ORM\JoinColumn(name="f_stade_id", referencedColumnName="id", nullable=true)
     * @var Stades|null
     */
    private ?Stades $fStades = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private ?string $logo = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $franchise = false;

    /**
     * @ORM\OneToMany(targetEntity=Penalite::class, mappedBy="equipe", orphanRemoval=true, cascade={"remove"})
     */
    private Collection $penalite;

    /**
     * @ORM\OneToMany(targetEntity=Players::class, mappedBy="ownedByTeam", orphanRemoval=true, cascade={"remove"})
     */
    private Collection $joueurs;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $ruleset = 0;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $score = 0;

    /**
     * @ORM\ManyToOne(targetEntity=RacesBb2020::class)
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=true)
     * @var RacesBb2020
     */
    private $race;

    /**
     * @ORM\ManyToOne(targetEntity=Sponsors::class, inversedBy="teamsSponsorisees")
     * @ORM\JoinColumn(name="sponsorId", referencedColumnName="id")
     */
    private $sponsor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $specialRulechoosed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $locked;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->penalite = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    /**
     * @param ArrayCollection $joueurs
     */
    public function setJoueurs(ArrayCollection $joueurs): void
    {
        $this->joueurs = $joueurs;
    }

    /**
     * @return Stades|null
     */
    public function getFStades(): ?Stades
    {
        return $this->fStades;
    }

    /**
     * @param Stades $fStades
     * @return Teams
     */
    public function setFStades(Stades $fStades): self
    {
        $this->fStades = $fStades;

        return $this;
    }

    public function getTeamId(): int
    {
        return $this->teamId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTreasury(): ?int
    {
        return $this->treasury;
    }

    public function setTreasury(?int $treasury): self
    {
        $this->treasury = $treasury;

        return $this;
    }

    public function getApothecary(): int
    {
        return $this->apothecary;
    }

    public function setApothecary(?int $apothecary): self
    {
        $this->apothecary = $apothecary;

        return $this;
    }

    public function getRerolls(): int
    {
        return $this->rerolls;
    }

    public function setRerolls(?int $rerolls): self
    {
        $this->rerolls = $rerolls;

        return $this;
    }

    public function getFfBought(): int
    {
        return $this->ffBought;
    }

    public function setFfBought(?int $ffBought): self
    {
        $this->ffBought = $ffBought;

        return $this;
    }

    public function getAssCoaches(): int
    {
        return $this->assCoaches;
    }

    public function setAssCoaches(?int $assCoaches): self
    {
        $this->assCoaches = $assCoaches;

        return $this;
    }

    public function getCheerleaders(): int
    {
        return $this->cheerleaders;
    }

    public function setCheerleaders(?int $cheerleaders): self
    {
        $this->cheerleaders = $cheerleaders;

        return $this;
    }

    public function getRetired(): bool
    {
        return $this->retired;
    }

    public function setRetired(bool $retired): self
    {
        $this->retired = $retired;

        return $this;
    }

    public function getFf(): int
    {
        return $this->ff;
    }

    public function setFf(int $ff): self
    {
        $this->ff = $ff;

        return $this;
    }

    public function getElo(): ?float
    {
        return $this->elo;
    }

    public function setElo(float $elo): self
    {
        $this->elo = $elo;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getOwnedByCoach(): ?Coaches
    {
        return $this->ownedByCoach;
    }

    /**
     * @param Coaches $ownedByCoach
     * @return Teams
     */
    public function setOwnedByCoach(Coaches $ownedByCoach): self
    {
        $this->ownedByCoach = $ownedByCoach;

        return $this;
    }

    public function getFRace(): ?Races
    {
        return $this->fRace;
    }

    /**
     * @param Races $fRace
     * @return Teams
     */
    public function setFRace(Races $fRace): self
    {
        $this->fRace = $fRace;

        return $this;
    }

    public function getTv(): ?int
    {
        return $this->tv;
    }

    /**
     * @param integer $tv
     * @return $this
     */
    public function setTv(int $tv): self
    {
        $this->tv = $tv;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getFranchise(): bool
    {
        return $this->franchise;
    }

    public function setFranchise(bool $franchise): self
    {
        $this->franchise = $franchise;

        return $this;
    }

    /**
     * @return Collection|Penalite[]
     */
    public function getPenalite(): Collection
    {
        return $this->penalite;
    }

    public function addPenalite(Penalite $penalite): self
    {
        if (!$this->penalite->contains($penalite)) {
            $this->penalite[] = $penalite;
            $penalite->setEquipe($this);
        }

        return $this;
    }

    public function removePenalite(Penalite $penalite): self
    {
        if ($this->penalite->contains($penalite)) {
            $this->penalite->removeElement($penalite);
            // set the owning side to null (unless already changed)
            if ($penalite->getEquipe() === $this) {
                $penalite->setEquipe(null);
            }
        }

        return $this;
    }

    public function getRuleset(): ?int
    {
        return $this->ruleset;
    }

    public function setRuleset(int $ruleset): self
    {
        $this->ruleset = $ruleset;

        return $this;
    }

    public function getRace(): ?RacesBb2020
    {
        return $this->race;
    }

    public function setRace(?RacesBb2020 $race): self
    {
        $this->race = $race;

        return $this;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param float $score
     * @return Teams
     */
    public function setScore(float $score): Teams
    {
        $this->score = $score;
        return $this;
    }

    public function getSponsor(): ?Sponsors
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsors $sponsor): self
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    public function getSpecialRulechoosed(): ?String
    {
        return $this->specialRulechoosed;
    }

    public function setSpecialRulechoosed(?String $specialRulechoosed): self
    {
        $this->specialRulechoosed = $specialRulechoosed;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }
}
