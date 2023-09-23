<?php

namespace App\Entity;

use App\Repository\RacesBb2020Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RacesBb2020Repository::class)
 */
class RacesBb2020
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $costRr;

    /**
     * @ORM\Column(type="string", length=45)
     * @var string
     */
    private $icon;

    /**
     * @ORM\ManyToMany(targetEntity=SpecialRule::class, inversedBy="racesBb2020s")
     */
    private $specialRule;

    public function __construct()
    {
        $this->specialRule = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCostRr(): ?int
    {
        return $this->costRr;
    }

    public function setCostRr(int $costRr): self
    {
        $this->costRr = $costRr;

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

    /**
     * @return Collection<int, SpecialRule>
     */
    public function getSpecialRule(): Collection
    {
        return $this->specialRule;
    }

    public function addSpecialRule(SpecialRule $specialRule): self
    {
        if (!$this->specialRule->contains($specialRule)) {
            $this->specialRule[] = $specialRule;
        }

        return $this;
    }

    public function removeSpecialRule(SpecialRule $specialRule): self
    {
        $this->specialRule->removeElement($specialRule);

        return $this;
    }
}
