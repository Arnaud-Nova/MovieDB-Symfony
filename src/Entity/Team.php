<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * ManyToOne+inversedBy = 1,1
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="teams")
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Job", inversedBy="teams")
     */
    private $job;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person", inversedBy="teams")
     */
    private $person;

    public function getId()
    {
        return $this->id;
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

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

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

    public function __toString(){
          // je dois forcer le type en chaine de caractere car je en peux pas retourner autre chose qu'une chaine
          //ici id est un entier
        return strval($this->id); 
    }
}
