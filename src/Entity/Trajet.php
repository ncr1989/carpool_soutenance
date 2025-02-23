<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ville::class,inversedBy:"trajetsArrivee")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villeArrivee = null;

    #[ORM\ManyToOne(targetEntity: Ville::class,inversedBy:"trajetsDepart" )]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villeDepart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTrajet = null;

    // Relations entre trajet et personne
    #[ORM\ManyToOne(targetEntity: Personne::class, inversedBy:"trajetsProposes")]
    #[ORM\JoinColumn(name: "personne_id", referencedColumnName: "id", nullable: false)]
    private $personne = null;

    #[ORM\ManyToMany(targetEntity:Personne::class, mappedBy:"trajetsReserves")]
    private Collection $passagers;

    #[ORM\Column]
    private ?int $nbrPlaces = null;

    public function __construct() {
        $this->passagers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleArrivee(): ?Ville
    {
        return $this->villeArrivee;
    }

    public function setVilleArrivee(?Ville $villeArrivee): static
    {
        $this->villeArrivee = $villeArrivee;
        return $this;
    }

    public function getVilleDepart(): ?Ville
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(?Ville $villeDepart): static
    {
        $this->villeDepart = $villeDepart;
        return $this;
    }


    public function getDateTrajet(): ?\DateTimeInterface
    {
        return $this->dateTrajet;
    }

    public function setDateTrajet(\DateTimeInterface $dateTrajet): static
    {
        $this->dateTrajet = $dateTrajet;
        return $this;
    }

    public function getNbrPlaces(): ?int
    {
        return $this->nbrPlaces;
    }

    public function setNbrPlaces(int $nbrPlaces): static
    {
        $this->nbrPlaces = $nbrPlaces;
        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;
        return $this;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPassagers(): Collection
    {
        return $this->passagers;
    }

    public function addPassager(Personne $passager): static
    {
        if (!$this->passagers->contains($passager)) {
            $this->passagers->add($passager);
            $passager->addTrajetsReserves($this);
        }
        return $this;
    }

    public function removePassager(Personne $passager): static
    {
        if ($this->passagers->removeElement($passager)) {
            $passager->removeTrajetsReserves($this);
        }
        return $this;
    }
}