<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Players
 *
 * @ORM\Table(name="players", indexes={
 *     @ORM\Index(name="idx_owned_by_team_id", columns={"owned_by_team_id"}),
 *     @ORM\Index(name="fk_players_game_data_players1_idx", columns={"f_pos_id"}),
 *      *     @ORM\Index(name="fk_players_races1_idx", columns={"f_rid"}),
 *     @ORM\Index(name="fk_players_coaches1_idx", columns={"f_cid"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlayersRepository")
 */
class Players
{
    /**
     * @var int
     *
     * @ORM\Column(name="player_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $playerId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nr", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $nr;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_bought", type="datetime", nullable=true)
     */
    private $dateBought;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_sold", type="datetime", nullable=true)
     */
    private $dateSold;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ach_ma", type="integer", nullable=true)
     */
    private $achMa = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ach_st", type="integer", nullable=true)
     */
    private $achSt = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ach_ag", type="integer", nullable=true)
     */
    private $achAg = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ach_av", type="integer", nullable=true)
     */
    private $achAv = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="extra_spp", type="integer", nullable=true)
     */
    private $extraSpp;

    /**
     * @var int|null
     *
     * @ORM\Column(name="extra_val", type="integer", nullable=false)
     */
    private $extraVal = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="value", type="integer", nullable=true)
     */
    private $value;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_died", type="datetime", nullable=true)
     */
    private $dateDied;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inj_ma", type="integer", nullable=true)
     */
    private $injMa = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inj_st", type="integer", nullable=true)
     */
    private $injSt = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inj_ag", type="integer", nullable=true)
     */
    private $injAg = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inj_av", type="integer", nullable=true)
     */
    private $injAv = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inj_ni", type="integer", nullable=true)
     */
    private $injNi = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="inj_rpm", type="integer", nullable=false)
     */
    private $injRpm = 0;

    /**
     * @var GameDataPlayers
     *
     * @ORM\ManyToOne(targetEntity="GameDataPlayers", fetch="EAGER")
     * @ORM\JoinColumn(name="f_pos_id", referencedColumnName="pos_id")
     */
    private $fPos;

    /**
     * @var Races
     *
     * @ORM\ManyToOne(targetEntity="Races", fetch="EAGER")
     * @ORM\JoinColumn(name="f_rid", referencedColumnName="race_id")
     */
    private $fRid;

    /**
     * @var Teams
     *
     * @ORM\ManyToOne(targetEntity="Teams", fetch="EAGER")
     *  @ORM\JoinColumn(name="owned_by_team_id", referencedColumnName="team_id")
     */
    private $ownedByTeam;

    /**
     * @var Coaches
     *
     * @ORM\ManyToOne(targetEntity="Coaches", fetch="EAGER")
     *   @ORM\JoinColumn(name="f_cid", referencedColumnName="coach_id")
     */
    private $fCid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlayersIcons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $icon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

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

    public function getDateBought(): DateTime
    {
        return $this->dateBought;
    }

    public function setDateBought(DateTime $dateBought): self
    {
        $this->dateBought = $dateBought;

        return $this;
    }

    public function getDateSold(): DateTime
    {
        return $this->dateSold;
    }

    public function setDateSold(DateTime $dateSold): self
    {
        $this->dateSold = $dateSold;

        return $this;
    }

    public function getAchMa(): ?int
    {
        return $this->achMa;
    }

    public function setAchMa(int $achMa): self
    {
        $this->achMa = $achMa;

        return $this;
    }

    public function getAchSt(): ?int
    {
        return $this->achSt;
    }

    public function setAchSt(int $achSt): self
    {
        $this->achSt = $achSt;

        return $this;
    }

    public function getAchAg(): ?int
    {
        return $this->achAg;
    }

    public function setAchAg(int $achAg): self
    {
        $this->achAg = $achAg;

        return $this;
    }

    public function getAchAv(): ?int
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

    public function getExtraVal(): ?int
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

    public function getDateDied(): ?DateTime
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

    public function getInjMa(): ?int
    {
        return $this->injMa;
    }

    public function setInjMa(int $injMa): self
    {
        $this->injMa = $injMa;

        return $this;
    }

    public function getInjSt(): ?int
    {
        return $this->injSt;
    }

    public function setInjSt(int $injSt): self
    {
        $this->injSt = $injSt;

        return $this;
    }

    public function getInjAg(): ?int
    {
        return $this->injAg;
    }

    public function setInjAg(int $injAg): self
    {
        $this->injAg = $injAg;

        return $this;
    }

    public function getInjAv(): ?int
    {
        return $this->injAv;
    }

    public function setInjAv(int $injAv): self
    {
        $this->injAv = $injAv;

        return $this;
    }

    public function getInjNi(): ?int
    {
        return $this->injNi;
    }

    public function setInjNi(int $injNi): self
    {
        $this->injNi = $injNi;

        return $this;
    }

    public function getInjRpm(): ?int
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

    public function getFCid(): ?Coaches
    {
        return $this->fCid;
    }

    public function setFCid(Coaches $fCid): self
    {
        $this->fCid = $fCid;

        return $this;
    }

    public function getIcon(): ?PlayersIcons
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
}
