<?php

namespace App\Entity;

use App\Repository\SpecialRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecialRuleRepository::class)
 */
class SpecialRule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=RacesBb2020::class, mappedBy="specialRule")
     */
    private $racesBb2020s;

    public function __construct()
    {
        $this->racesBb2020s = new ArrayCollection();
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

    /**
     * @return Collection<int, RacesBb2020>
     */
    public function getRacesBb2020s(): Collection
    {
        return $this->racesBb2020s;
    }

    public function addRacesBb2020(RacesBb2020 $racesBb2020): self
    {
        if (!$this->racesBb2020s->contains($racesBb2020)) {
            $this->racesBb2020s[] = $racesBb2020;
            $racesBb2020->addSpecialRule($this);
        }

        return $this;
    }

    public function removeRacesBb2020(RacesBb2020 $racesBb2020): self
    {
        if ($this->racesBb2020s->removeElement($racesBb2020)) {
            $racesBb2020->removeSpecialRule($this);
        }

        return $this;
    }

}
