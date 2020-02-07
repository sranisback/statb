<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayersSkills
 *
 * @ORM\Table(name="players_skills", indexes={@ORM\Index(name="fk_players_skills_players1_idx", columns={"f_pid"}),
 * @ORM\Index(name="fk_players_skills_game_data_skills1_idx", columns={"f_skill_id"})})
 * @ORM\Entity
 */
class PlayersSkills
{
    /**
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private int $id;

    /**
     *
     * @ORM\Column(name="type", type="string", length=1, nullable=true)
     * @var null|string
     */
    private ?string $type = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="GameDataSkills", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="f_skill_id", referencedColumnName="skill_id")
     * })
     * @var \App\Entity\GameDataSkills|null
     */
    private ?\App\Entity\GameDataSkills $fSkill;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Players", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="f_pid", referencedColumnName="player_id")
     * })
     * @var \App\Entity\Players|null
     */
    private ?\App\Entity\Players $fPid;

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFSkill(): ?GameDataSkills
    {
        return $this->fSkill;
    }

    public function setFSkill(GameDataSkills $fSkill): self
    {
        $this->fSkill = $fSkill;

        return $this;
    }

    public function getFPid(): ?Players
    {
        return $this->fPid;
    }

    public function setFPid(Players $fPid): self
    {
        $this->fPid = $fPid;

        return $this;
    }
}
