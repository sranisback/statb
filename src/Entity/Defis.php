<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DefisRepository")
 */
class Defis
{
    /**
     * @ORM\Id
     * @var int|null
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", cascade={"persist"})
     * @ORM\JoinColumn(name="equipe_Defiee", referencedColumnName="team_id")
     * @var null|\App\Entity\Teams
     */
    private ?\App\Entity\Teams $equipeDefiee = null;

    /**
     * @ORM\Column(type="boolean")
     * @var int
     */
    private int $defieRealise = 0;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Matches", cascade={"persist"})
     * @ORM\JoinColumn(name="match_Defie", referencedColumnName="match_id")
     * @var \App\Entity\Matches|null
     */
    private ?\App\Entity\Matches $matchDefi;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $dateDefi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams")
     * @ORM\JoinColumn(name="equipe_Origine", referencedColumnName="team_id")
     * @var null|\App\Entity\Teams
     */
    private ?\App\Entity\Teams $equipeOrigine = null;

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

    public function getDefieRealise(): int
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

    public function getDateDefi(): ?\DateTimeInterface
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
