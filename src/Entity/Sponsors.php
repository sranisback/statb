<?php

namespace App\Entity;

use App\Repository\SponsorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SponsorsRepository::class)
 */
class Sponsors
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Teams::class, mappedBy="sponsor")
     */
    private $teamsSponsorisees;

    public function __construct()
    {
        $this->teamsSponsorisees = new ArrayCollection();
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
     * @return Collection<int, Teams>
     */
    public function getTeamsSponsorisees(): Collection
    {
        return $this->teamsSponsorisees;
    }

    public function addTeamsSponsorisee(Teams $teamsSponsorisee): self
    {
        if (!$this->teamsSponsorisees->contains($teamsSponsorisee)) {
            $this->teamsSponsorisees[] = $teamsSponsorisee;
            $teamsSponsorisee->setSponsor($this);
        }

        return $this;
    }

    public function removeTeamsSponsorisee(Teams $teamsSponsorisee): self
    {
        if ($this->teamsSponsorisees->removeElement($teamsSponsorisee)) {
            // set the owning side to null (unless already changed)
            if ($teamsSponsorisee->getSponsor() === $this) {
                $teamsSponsorisee->setSponsor(null);
            }
        }

        return $this;
    }
}
