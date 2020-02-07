<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Coaches
 *
 * @ORM\Table(name="coaches")
 * @ORM\Entity(repositoryClass="App\Repository\CoachesRepository")
 */
class Coaches implements UserInterface
{
    /**
     *
     * @ORM\Column(name="coach_id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $coachId;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private ?string $name;

    /**
     *
     * @ORM\Column(name="passwd", type="string", length=64, nullable=true)
     */
    private ?string $passwd;

    /**
     *
     * @ORM\Column(name="role", type="json", nullable=false)
     */
    private array $roles;

    public function getCoachId(): ?int
    {
        return $this->coachId;
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

    public function getPasswd(): ?string
    {
        return $this->passwd;
    }

    public function setPasswd(string $passwd): self
    {
        $this->passwd = $passwd;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->passwd;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->name;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->coachId,
            $this->name,
            $this->passwd,
        ));
    }

    /** @see \Serializable::unserialize()
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->coachId,
            $this->name,
            $this->passwd,
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }
}
