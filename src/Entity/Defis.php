<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DefisRepository")
 */
class Defis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", cascade={"persist"})
     * @ORM\JoinColumn(name="equipe_Defiee", referencedColumnName="team_id")
     */
    private ?\App\Entity\Teams $equipeDefiee;

    /**
     * @ORM\Column(type="boolean")
     */
    private int $defieRealise = 0;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Matches", cascade={"persist"})
     * @ORM\JoinColumn(name="match_Defie", referencedColumnName="match_id")
     */
    private ?\App\Entity\Matches $matchDefi;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $dateDefi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams")
     * @ORM\JoinColumn(name="equipe_Origine", referencedColumnName="team_id")
     */
    private ?\App\Entity\Teams $equipeOrigine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipeDefiee(): ?Teams
    {
        return $this->equipeDefiee;
    }

    public function setEquipeDefiee(Teams $equipeDefiee): self
    {
        $this->equipeDefiee = $equipeDefiee;

        return $this;
    }

    public function getDefieRealise(): ?bool
    {
        return $this->defieRealise;
    }

    public function setDefieRealise(bool $defieRealise): self
    {
        $this->defieRealise = $defieRealise;

        return $this;
    }

    public function getMatchDefi(): ?Matches
    {
        return $this->matchDefi;
    }

    public function setMatchDefi(?Matches $matchDefi): self
    {
        $this->matchDefi = $matchDefi;

        return $this;
    }

    public function getDateDefi(): ?\DateTime
    {
        return $this->dateDefi;
    }

    public function setDateDefi(\DateTime $dateDefi): self
    {
        $this->dateDefi = $dateDefi;

        return $this;
    }

    public function getEquipeOrigine(): ?Teams
    {
        return $this->equipeOrigine;
    }

    public function setEquipeOrigine(?Teams $equipeOrigine): self
    {
        $this->equipeOrigine = $equipeOrigine;

        return $this;
    }
}
