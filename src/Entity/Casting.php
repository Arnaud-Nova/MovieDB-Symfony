<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CastingRepository")
 */
class Casting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $role;

    /**
     * @ORM\Column(type="smallint")
     */
    private $orderCredit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person", inversedBy="castings", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="castings", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    public function getId()
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getOrderCredit(): ?int
    {
        return $this->orderCredit;
    }

    public function setOrderCredit(int $orderCredit): self
    {
        $this->orderCredit = $orderCredit;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function __toString()
    {
        return $this->role;
    }
}
