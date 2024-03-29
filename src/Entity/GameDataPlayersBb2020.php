<?php

namespace App\Entity;

use App\Repository\GameDataPlayersBb2020Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameDataPlayersBb2020Repository::class)
 */
class GameDataPlayersBb2020
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @var string
     */
    private $pos;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $cost;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $qty;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $ma;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $st;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $ag;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $cp;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $av;

    /**
     * @ORM\Column(type="string", length=6)
     * @var string
     */
    private $principales;

    /**
     * @ORM\Column(type="string", length=6)
     * @var string
     */
    private $secondaires;

    /**
     * @ORM\ManyToOne(targetEntity=RacesBb2020::class)
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=true)
     */
    public ?RacesBb2020 $race;

    /**
     * @ORM\ManyToMany(targetEntity=GameDataSkillsBb2020::class)
     * @ORM\JoinTable(name="GameDataPlayerBb2020_GameDataSkillsBb2020",
     *      joinColumns={@ORM\JoinColumn(name="position", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id")}
     *      )
     * @var Collection
     */
    private $baseSkills;

    public function __construct()
    {
        $this->baseSkills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPos(): ?string
    {
        return $this->pos;
    }

    public function setPos(string $pos): self
    {
        $this->pos = $pos;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getMa(): ?int
    {
        return $this->ma;
    }

    public function setMa(int $ma): self
    {
        $this->ma = $ma;

        return $this;
    }

    public function getSt(): ?int
    {
        return $this->st;
    }

    public function setSt(int $st): self
    {
        $this->st = $st;

        return $this;
    }

    public function getAg(): ?int
    {
        return $this->ag;
    }

    public function setAg(int $ag): self
    {
        $this->ag = $ag;

        return $this;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getAv(): ?int
    {
        return $this->av;
    }

    public function setAv(int $av): self
    {
        $this->av = $av;

        return $this;
    }

    public function getPrincipales(): ?string
    {
        return $this->principales;
    }

    public function setPrincipales(string $principales): self
    {
        $this->principales = $principales;

        return $this;
    }

    public function getSecondaires(): ?string
    {
        return $this->secondaires;
    }

    public function setSecondaires(string $secondaires): self
    {
        $this->secondaires = $secondaires;

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
     * @return Collection|GameDataSkillsBb2020[]
     */
    public function getBaseSkills(): Collection
    {
        return $this->baseSkills;
    }

    public function addBaseSkill(GameDataSkillsBb2020 $baseSkill): self
    {
        if (!$this->baseSkills->contains($baseSkill)) {
            $this->baseSkills[] = $baseSkill;
        }

        return $this;
    }

    public function removeBaseSkill(GameDataSkillsBb2020 $baseSkill): self
    {
        $this->baseSkills->removeElement($baseSkill);

        return $this;
    }
}
