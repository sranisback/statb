<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * GameDataPlayers
 *
 * @ORM\Table(name="game_data_players", indexes={
 *     @ORM\Index(name="fk_game_data_players_races1_idx", columns={"f_race_id"})})
 * @ORM\Entity
 */
class GameDataPlayers
{
    /**
     *
     * @ORM\Column(name="pos_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private int $posId;

    /**
     *
     * @ORM\Column(name="pos", type="string", length=60, nullable=true)
     * @var string|null
     */
    private ?string $pos = null;

    /**
     *
     * @ORM\Column(name="cost", type="integer", nullable=true, options={"unsigned"=true})
     * @var int
     */
    private ?int $cost = 0;

    /**
     *
     * @ORM\Column(name="qty", type="integer", nullable=true)
     * @var int
     */
    private ?int $qty = null;

    /**
     *
     * @ORM\Column(name="ma", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $ma = 0;

    /**
     *
     * @ORM\Column(name="st", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $st = 0;

    /**
     *
     * @ORM\Column(name="ag", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $ag = 0;

    /**
     *
     * @ORM\Column(name="av", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $av = 0;

    /**
     *
     * @ORM\Column(name="skills", type="string", length=79, nullable=true)
     * @var string|null
     */
    private ?string $skills = null;

    /**
     *
     * @ORM\Column(name="norm", type="string", length=6, nullable=true)
     * @var string|null
     */
    private ?string $norm = null;

    /**
     *
     * @ORM\Column(name="doub", type="string", length=6, nullable=true)
     * @var string|null
     */
    private ?string $doub = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Races")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="f_race_id", referencedColumnName="race_id", nullable=false)
     * })
     * @var Races|null
     */
    private ?Races $fRace = null;

    /**
     * @ORM\ManyToMany(targetEntity=GameDataSkills::class)
     * @ORM\JoinTable(name="GameDataPlayer_GameDataSkills",
     *      joinColumns={@ORM\JoinColumn(name="position", referencedColumnName="pos_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="skill_id")}
     *      )
     */
    private Collection $baseSkills;

    public function __construct()
    {
        $this->baseSkills = new ArrayCollection();
    }

    public function getPosId(): int
    {
        return $this->posId;
    }

    public function getPos(): ?string
    {
        return $this->pos;
    }

    public function setPos(?string $pos): self
    {
        $this->pos = $pos;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(?int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(?int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getMa(): ?int
    {
        return $this->ma;
    }

    public function setMa(?int $ma): self
    {
        $this->ma = $ma;

        return $this;
    }

    public function getSt(): ?int
    {
        return $this->st;
    }

    public function setSt(?int $st): self
    {
        $this->st = $st;

        return $this;
    }

    public function getAg(): ?int
    {
        return $this->ag;
    }

    public function setAg(?int $ag): self
    {
        $this->ag = $ag;

        return $this;
    }

    public function getAv(): ?int
    {
        return $this->av;
    }

    public function setAv(?int $av): self
    {
        $this->av = $av;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getNorm(): ?string
    {
        return $this->norm;
    }

    public function setNorm(?string $norm): self
    {
        $this->norm = $norm;

        return $this;
    }

    public function getDoub(): ?string
    {
        return $this->doub;
    }

    public function setDoub(?string $doub): self
    {
        $this->doub = $doub;

        return $this;
    }

    public function getFRace(): ?Races
    {
        return $this->fRace;
    }

    public function setFRace(Races $fRace): self
    {
        $this->fRace = $fRace;

        return $this;
    }

    /**
     * @return Collection|GameDataSkills[]
     */
    public function getBaseSkills(): Collection
    {
        return $this->baseSkills;
    }

    public function addBaseSkill(GameDataSkills $baseSkill): self
    {
        if (!$this->baseSkills->contains($baseSkill)) {
            $this->baseSkills[] = $baseSkill;
        }

        return $this;
    }

    public function removeBaseSkill(GameDataSkills $baseSkill): self
    {
        if ($this->baseSkills->contains($baseSkill)) {
            $this->baseSkills->removeElement($baseSkill);
        }

        return $this;
    }
}
