<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerIconsRepository")
 */
class PlayersIcons
{
    /**
     * @ORM\Id
     * @var int|null
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=50)
     * @var string|null
     */
    private ?string $iconName = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GameDataPlayers")
     * @ORM\JoinColumn(name="f_pos_id", referencedColumnName="pos_id")
     * @var null|GameDataPlayers
     */
    private ?GameDataPlayers $position = null;

    /**
     * @ORM\ManyToOne(targetEntity=GameDataPlayersBb2020::class)
     * @ORM\JoinColumn(name="pos_id", referencedColumnName="id")
     * @var GameDataPlayersBb2020
     */
    private $positionBb2020;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIconName(): ?string
    {
        return $this->iconName;
    }

    public function setIconName(string $iconName): self
    {
        $this->iconName = $iconName;

        return $this;
    }

    public function getPosition(): ?GameDataPlayers
    {
        return $this->position;
    }

    public function setPosition(?GameDataPlayers $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPositionBb2020(): ?GameDataPlayersBb2020
    {
        return $this->positionBb2020;
    }

    public function setPositionBb2020(?GameDataPlayersBb2020 $positionBb2020): self
    {
        $this->positionBb2020 = $positionBb2020;

        return $this;
    }
}
