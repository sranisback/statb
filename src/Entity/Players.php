<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Players
 *
 * @ORM\Table(name="players", indexes={
 *     @ORM\Index(name="idx_owned_by_team_id", columns={"owned_by_team_id"}),
 *     @ORM\Index(name="fk_players_game_data_players1_idx", columns={"f_pos_id"}),
 *      *     @ORM\Index(name="fk_players_races1_idx", columns={"f_rid"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlayersRepository")
 */
class Players
{
    /**
     *
     * @ORM\Column(name="player_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private ?int $playerId = 0;

    /**
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $type = null;

    /**
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     * @var null|string
     */
    private ?string $name = null;

    /**
     *
     * @ORM\Column(name="nr", type="integer", nullable=true, options={"unsigned"=true})
     * @var int|null
     */
    private ?int $nr = null;

    /**
     *
     * @ORM\Column(name="date_bought", type="datetime", nullable=true)
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $dateBought = null;

    /**
     *
     * @ORM\Column(name="date_sold", type="datetime", nullable=true)
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $dateSold = null;

    /**
     *
     * @ORM\Column(name="ach_ma", type="integer", nullable=true)
     * @var int
     */
    private int $achMa = 0;

    /**
     *
     * @ORM\Column(name="ach_st", type="integer", nullable=true)
     * @var int
     */
    private int $achSt = 0;

    /**
     *
     * @ORM\Column(name="ach_ag", type="integer", nullable=true)
     * @var int
     */
    private int $achAg = 0;

    /**
     *
     * @ORM\Column(name="ach_av", type="integer", nullable=true)
     * @var int
     */
    private int $achAv = 0;

    /**
     *
     * @ORM\Column(name="extra_spp", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $extraSpp = null;

    /**
     *
     * @ORM\Column(name="extra_val", type="integer", nullable=false)
     * @var int
     */
    private int $extraVal = 0;

    /**
     *
     * @ORM\Column(name="value", type="integer", nullable=true)
     * @var int|null
     */
    private ?int $value = null;

    /**
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     * @var int
     */
    private ?int $status = 0;

    /**
     *
     * @ORM\Column(name="date_died", type="datetime", nullable=true)
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $dateDied = null;

    /**
     *
     * @ORM\Column(name="inj_ma", type="integer", nullable=true)
     * @var int
     */
    private int $injMa = 0;

    /**
     *
     * @ORM\Column(name="inj_st", type="integer", nullable=true)
     * @var int
     */
    private int $injSt = 0;

    /**
     *
     * @ORM\Column(name="inj_ag", type="integer", nullable=true)
     * @var int
     */
    private int $injAg = 0;

    /**
     *
     * @ORM\Column(name="inj_av", type="integer", nullable=true)
     * @var int
     */
    private int $injAv = 0;

    /**
     *
     * @ORM\Column(name="inj_ni", type="integer", nullable=true)
     * @var int
     */
    private int $injNi = 0;

    /**
     *
     * @ORM\Column(name="inj_rpm", type="integer", nullable=false)
     * @var int
     */
    private int $injRpm = 0;

    /**
     *
     * @ORM\ManyToOne(targetEntity="GameDataPlayers")
     * @ORM\JoinColumn(name="f_pos_id", referencedColumnName="pos_id")
     * @var null|\App\Entity\GameDataPlayers
     */
    private ?\App\Entity\GameDataPlayers $fPos = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Races")
     * @ORM\JoinColumn(name="f_rid", referencedColumnName="race_id")
     * @var \App\Entity\Races|null
     */
    private ?\App\Entity\Races $fRid = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Teams", inversedBy="joueurs")
     * @ORM\JoinColumn (name="owned_by_team_id", referencedColumnName="team_id")
     * @var \App\Entity\Teams|null
     */
    private ?\App\Entity\Teams $ownedByTeam = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlayersIcons")
     * @ORM\JoinColumn(nullable=false)
     * @var \App\Entity\PlayersIcons
     */
    private \App\Entity\PlayersIcons $icon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private ?string $photo = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueBlessure", mappedBy="Player", orphanRemoval=true, cascade={"persist"})
     * @var \Doctrine\Common\Collections\Collection
     */
    private \Doctrine\Common\Collections\Collection $historiqueBlessures;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MatchData", mappedBy="fPlayer", orphanRemoval=true, cascade={"remove"})
     * @var \Doctrine\Common\Collections\Collection
     */
    private Collection $matchData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlayersSkills", mappedBy="fPid", orphanRemoval=true, cascade={"remove"})
     * @var \App\Entity\PlayersSkills[]|\Doctrine\Common\Collections\Collection
     */
    private Collection $skills;

    public function __construct()
    {
        $this->historiqueBlessures = new ArrayCollection();
        $this->matchData = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatchData(): \Doctrine\Common\Collections\Collection
    {
        return $this->matchData;
    }

    /**
     * @param ArrayCollection $matchData
     */
    public function setMatchData(ArrayCollection $matchData): void
    {
        $this->matchData = $matchData;
    }

    public function getPlayerId(): ?int
    {
        return $this->playerId;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getNr(): ?int
    {
        return $this->nr;
    }

    public function setNr(int $nr): self
    {
        $this->nr = $nr;

        return $this;
    }

    public function getDateBought(): ?\DateTimeInterface
    {
        return $this->dateBought;
    }

    public function setDateBought(DateTime $dateBought): self
    {
        $this->dateBought = $dateBought;

        return $this;
    }

    public function getDateSold(): ?\DateTimeInterface
    {
        return $this->dateSold;
    }

    public function setDateSold(DateTime $dateSold): self
    {
        $this->dateSold = $dateSold;

        return $this;
    }

    public function getAchMa(): int
    {
        return $this->achMa;
    }

    public function setAchMa(int $achMa): self
    {
        $this->achMa = $achMa;

        return $this;
    }

    public function getAchSt(): int
    {
        return $this->achSt;
    }

    public function setAchSt(int $achSt): self
    {
        $this->achSt = $achSt;

        return $this;
    }

    public function getAchAg(): int
    {
        return $this->achAg;
    }

    public function setAchAg(int $achAg): self
    {
        $this->achAg = $achAg;

        return $this;
    }

    public function getAchAv(): int
    {
        return $this->achAv;
    }

    public function setAchAv(int $achAv): self
    {
        $this->achAv = $achAv;

        return $this;
    }

    public function getExtraSpp(): ?int
    {
        return $this->extraSpp;
    }

    public function setExtraSpp(int $extraSpp): self
    {
        $this->extraSpp = $extraSpp;

        return $this;
    }

    public function getExtraVal(): int
    {
        return $this->extraVal;
    }

    public function setExtraVal(int $extraVal): self
    {
        $this->extraVal = $extraVal;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    //1 - ok
    //7 - vendu
    //8 - mort
    //9 - px

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateDied(): ?\DateTimeInterface
    {
        return $this->dateDied;
    }

    /**
     * @param DateTime $dateDied
     * @return Players
     */
    public function setDateDied(DateTime $dateDied): self
    {
        $this->dateDied = $dateDied;

        return $this;
    }

    public function getInjMa(): int
    {
        return $this->injMa;
    }

    public function setInjMa(int $injMa): self
    {
        $this->injMa = $injMa;

        return $this;
    }

    public function getInjSt(): int
    {
        return $this->injSt;
    }

    public function setInjSt(int $injSt): self
    {
        $this->injSt = $injSt;

        return $this;
    }

    public function getInjAg(): int
    {
        return $this->injAg;
    }

    public function setInjAg(int $injAg): self
    {
        $this->injAg = $injAg;

        return $this;
    }

    public function getInjAv(): int
    {
        return $this->injAv;
    }

    public function setInjAv(int $injAv): self
    {
        $this->injAv = $injAv;

        return $this;
    }

    public function getInjNi(): int
    {
        return $this->injNi;
    }

    public function setInjNi(int $injNi): self
    {
        $this->injNi = $injNi;

        return $this;
    }

    public function getInjRpm(): int
    {
        return $this->injRpm;
    }

    public function setInjRpm(int $injRpm): self
    {
        $this->injRpm = $injRpm;

        return $this;
    }

    public function getFPos(): ?GameDataPlayers
    {
        return $this->fPos;
    }

    public function setFPos(GameDataPlayers $fPos): self
    {
        $this->fPos = $fPos;

        return $this;
    }

    public function getFRid(): ?Races
    {
        return $this->fRid;
    }

    public function setFRid(Races $fRid): self
    {
        $this->fRid = $fRid;

        return $this;
    }

    public function getOwnedByTeam(): ?Teams
    {
        return $this->ownedByTeam;
    }

    public function setOwnedByTeam(Teams $ownedByTeam): self
    {
        $this->ownedByTeam = $ownedByTeam;

        return $this;
    }

    public function getIcon(): \App\Entity\PlayersIcons
    {
        return $this->icon;
    }

    public function setIcon(?PlayersIcons $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getHistoriqueBlessures(): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->historiqueBlessures;
    }

    public function addHistoriqueBlessure(HistoriqueBlessure $historiqueBlessure): self
    {
        if (!$this->historiqueBlessures->contains($historiqueBlessure)) {
            $this->historiqueBlessures[] = $historiqueBlessure;
            $historiqueBlessure->setPlayer($this);
        }

        return $this;
    }

    public function removeHistoriqueBlessure(HistoriqueBlessure $historiqueBlessure): self
    {
        if ($this->historiqueBlessures->contains($historiqueBlessure)) {
            $this->historiqueBlessures->removeElement($historiqueBlessure);
            // set the owning side to null (unless already changed)
            if ($historiqueBlessure->getPlayer() === $this) {
                $historiqueBlessure->setPlayer(null);
            }
        }

        return $this;
    }
}
