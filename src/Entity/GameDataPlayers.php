<?php

namespace App\Entity;

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
     * @var int
     *
     * @ORM\Column(name="pos_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $posId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pos", type="string", length=60, nullable=true)
     */
    private $pos;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cost", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $cost;

    /**
     * @var int|null
     *
     * @ORM\Column(name="qty", type="integer", nullable=true)
     */
    private $qty;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ma", type="integer", nullable=true)
     */
    private $ma;

    /**
     * @var int|null
     *
     * @ORM\Column(name="st", type="integer", nullable=true)
     */
    private $st;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ag", type="integer", nullable=true)
     */
    private $ag;

    /**
     * @var int|null
     *
     * @ORM\Column(name="av", type="integer", nullable=true)
     */
    private $av;

    /**
     * @var string|null
     *
     * @ORM\Column(name="skills", type="string", length=79, nullable=true)
     */
    private $skills;

    /**
     * @var string|null
     *
     * @ORM\Column(name="norm", type="string", length=6, nullable=true)
     */
    private $norm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="doub", type="string", length=6, nullable=true)
     */
    private $doub;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=25, nullable=false)
     */
    private $icon;

    /**
     * @var Races
     *
     * @ORM\ManyToOne(targetEntity="Races")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="f_race_id", referencedColumnName="race_id", nullable=false)
     * })
     */
    private $fRace;

    public function getPosId(): ?int
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

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
}
