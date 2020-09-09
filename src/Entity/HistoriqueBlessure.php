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
     * @ORM\Id
     * @var int|null
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private ?int $blessure = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Players", inversedBy="historiqueBlessures")
     * @ORM\JoinColumn(name="fplayer", referencedColumnName="player_id")
     * @var null|\App\Entity\Players
     */
    private ?\App\Entity\Players $Player = null;

    /**
     *
     * @ORM\Column(type="date")
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $date= null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matches", inversedBy="blessuresMatch")
     * @ORM\JoinColumn(name="matches", referencedColumnName="match_id")
     * @var \App\Entity\Matches|null
     */
    private ?\App\Entity\Matches $fmatch= null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlessure(): int
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

    public function getDate(): ?\DateTimeInterface
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
