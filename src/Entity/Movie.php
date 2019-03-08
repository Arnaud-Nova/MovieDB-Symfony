<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Utils\Slugger;

/*
 Le but de ce subscriber est d'activer les événements doctrine afin que le slug s'effectuer à l'enregistrement et à la mise à jour d'un film (applySlug())
*/

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 *  * @ORM\HasLifecycleCallbacks()
 */
class Movie
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
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", inversedBy="movies",cascade={"persist"})
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Casting", mappedBy="movie")
     */
    private $castings;

    /**
     * OneToMany+mappedBy = 1,n
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="movie")
     */
    private $teams;

    /**
     * @ORM\Column(type="smallint")
     */
    private $score;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="datetime")
     */
    private $productionDate;

    /**
     * @Assert\File(
     * maxSize = "1024k", 
     * mimeTypes={ "image/gif", "image/jpeg", "image/png" },
     * mimeTypesMessage = "Please valid image format : gif, png, jpeg"
     * )
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $poster;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $posterUrl;

    public function __construct()
    {
        $this->castings = new ArrayCollection();

        // la relation étant faite sur 2 temps different, le constructeur est apparement reecrit et supprime l'init de $genres
        $this->genres = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->score = 0;
        $this->productionDate = new DateTime();
    }

   /*
    Activer via annotation le(s) évenement(s) qui vont déclencher la fonction applySlug(), je peux cumuler plusieurs events sur lesquels appliquer ma fonction
    ici avant enregistrement et avant update
    
    */
    /**
     * ORM\PrePersist (on retire le @ devant ORM pour emêcher l'activation de l'event)
     * ORM\PreUpdate
     */
    public function applySlug(){

       /*
            Réaliser le code nécessaire permettant d'instancier un objet du type Slugger
            qui pourra réaliser les action nécessaires sur la propriété à modifier attendue
       */
        // lignes commentées pour ne pas fair doublon avec l'event Listener créé par la suite 
        // $slugger = new Slugger(true);
        // $this->slug = $slugger->sluggify($this->title);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
       //genere un bug avec faker 
       // if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
       // }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    /**
     * @return Collection|Casting[]
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->contains($casting)) {
            $this->castings->removeElement($casting);
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setMovie($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getMovie() === $this) {
                $team->setMovie(null);
            }
        }

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getProductionDate(): ?\DateTimeInterface
    {
        return $this->productionDate;
    }

    public function setProductionDate(\DateTimeInterface $productionDate): self
    {
        $this->productionDate = $productionDate;

        return $this;
    }


    public function getPoster() //ATTENTION A NE PAS LAISSER LE TYPE STRING => sinon pb de recuperation de l'objet image au niveau handlerequest
    {
        return $this->poster;
    }

    public function setPoster($poster): self //IDEM
    {
        $this->poster = $poster;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPosterUrl(): ?string
    {
        return $this->posterUrl;
    }

    public function setPosterUrl(?string $posterUrl): self
    {
        $this->posterUrl = $posterUrl;

        return $this;
    }
}
