<?php

namespace App\Entity;

use App\Repository\InfosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfosRepository::class)
 */
class Infos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $messages;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessages(): ?string
    {
        return $this->messages;
    }

    public function setMessages(?string $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
