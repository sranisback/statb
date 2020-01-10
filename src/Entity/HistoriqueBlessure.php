<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueBlessureRepository")
 */
class HistoriqueBlessure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $blessure;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Players", inversedBy="historiqueBlessures")
     * @ORM\JoinColumn(name="fplayer", referencedColumnName="player_id")
     */
    private $Player;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matches", inversedBy="blessuresMatch")
     * @ORM\JoinColumn(name="matches", referencedColumnName="match_id")
     */
    private $fmatch;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlessure(): ?int
    {
        return $this->blessure;
    }

    public function setBlessure(int $blessure): self
    {
        $this->blessure = $blessure;

        return $this;
    }

    public function getPlayer(): ?Players
    {
        return $this->Player;
    }

    public function setPlayer(?Players $Player): self
    {
        $this->Player = $Player;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFmatch(): ?Matches
    {
        return $this->fmatch;
    }

    public function setFmatch(?Matches $fmatch): self
    {
        $this->fmatch = $fmatch;

        return $this;
    }
}
