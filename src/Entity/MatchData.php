<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchData
 *
 * @ORM\Table(name="match_data", indexes={@ORM\Index(name="idx_m", columns={"f_match_id"}),
 *     @ORM\Index(name="idx_p_m", columns={"f_player_id", "f_match_id"}),
 *     @ORM\Index(name="idx_p_tr", columns={"f_player_id"}),
 *     @ORM\Index(name="idx_t_m", columns={"f_match_id"}),
 *     @ORM\Index(name="idx_r_m", columns={"f_match_id"}),
 *     @ORM\Index(name="idx_c_m", columns={"f_match_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\MatchDataRepository")
 */
class MatchData
{
    /**
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private int $id;

    /**
     *
     * @ORM\Column(name="mvp", type="integer", nullable=true)
     * @var int
     */
    private ?int $mvp = 0;

    /**
     *
     * @ORM\Column(name="cp", type="integer", nullable=true)
     * @var int
     */
    private ?int $cp = 0;

    /**
     *
     * @ORM\Column(name="td", type="integer", nullable=true)
     * @var int
     */
    private ?int $td = 0;

    /**
     *
     * @ORM\Column(name="intcpt", type="integer", nullable=true)
     * @var int
     */
    private ?int $intcpt = 0;

    /**
     *
     * @ORM\Column(name="bh", type="integer", nullable=true)
     * @var int
     */
    private ?int $bh = 0;

    /**
     *
     * @ORM\Column(name="si", type="integer", nullable=true)
     * @var int
     */
    private ?int $si = 0;

    /**
     *
     * @ORM\Column(name="ki", type="integer", nullable=true)
     * @var int
     */
    private ?int $ki = 0;

    /**
     *
     * @ORM\Column(name="inj", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $inj = 0;

    /**
     *
     * @ORM\Column(name="agg", type="integer", nullable=true)
     * @var int
     */
    private ?int $agg = 0;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Players", inversedBy="matchData")
     * @ORM\JoinColumn(name="f_player_id", referencedColumnName="player_id")
     * @var \App\Entity\Players|null
     */
    private ?\App\Entity\Players $fPlayer = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Matches")
     * @ORM\JoinColumn(name="f_match_id", referencedColumnName="match_id")
     * @var \App\Entity\Matches|null
     */
    private ?\App\Entity\Matches $fMatch = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMvp(): ?int
    {
        return $this->mvp;
    }

    public function setMvp(?int $mvp): self
    {
        $this->mvp = $mvp;

        return $this;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(?int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getTd(): ?int
    {
        return $this->td;
    }

    public function setTd(?int $td): self
    {
        $this->td = $td;

        return $this;
    }

    public function getIntcpt(): ?int
    {
        return $this->intcpt;
    }

    public function setIntcpt(?int $intcpt): self
    {
        $this->intcpt = $intcpt;

        return $this;
    }

    public function getBh(): ?int
    {
        return $this->bh;
    }

    public function setBh(?int $bh): self
    {
        $this->bh = $bh;

        return $this;
    }

    public function getSi(): ?int
    {
        return $this->si;
    }

    public function setSi(?int $si): self
    {
        $this->si = $si;

        return $this;
    }

    public function getKi(): ?int
    {
        return $this->ki;
    }

    public function setKi(?int $ki): self
    {
        $this->ki = $ki;

        return $this;
    }

    public function getInj(): ?int
    {
        return $this->inj;
    }

    public function setInj(?int $inj): self
    {
        $this->inj = $inj;

        return $this;
    }

    public function getAgg(): ?int
    {
        return $this->agg;
    }

    public function setAgg(?int $agg): self
    {
        $this->agg = $agg;

        return $this;
    }

    public function getFPlayer(): ?Players
    {
        return $this->fPlayer;
    }

    public function setFPlayer(Players $fPlayer): self
    {
        $this->fPlayer = $fPlayer;

        return $this;
    }

    public function getFMatch(): ?Matches
    {
        return $this->fMatch;
    }

    public function setFMatch(Matches $fMatch): self
    {
        $this->fMatch = $fMatch;

        return $this;
    }
}
