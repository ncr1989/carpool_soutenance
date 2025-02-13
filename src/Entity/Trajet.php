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

    #[ORM\Column(length: 255)]
    private ?string $villeA = null;

    #[ORM\Column(length: 255)]
    private ?string $villeD = null;

    #[ORM\Column]
    private ?bool $conducteur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateTrajet = null;

     // Relations entre trajet et personne
     #[ORM\ManyToOne(targetEntity: Personne::class, inversedBy:"trajetsProposes")]
     #[ORM\JoinColumn(name: "personne_id", referencedColumnName: "id", nullable: false)]
     private ?Personne $personne = null;

     #[ORM\ManyToOne(targetEntity:Ville::class,inversedBy:"trajets")]
     #[ORM\JoinColumn(name: "ville_id", referencedColumnName: "id", nullable: false)]
     private ?Trajet $trajet;


     #[ORM\ManyToMany(targetEntity:Personne::class, mappedBy:"trajetReserves")]
     private Collection $passagers;

     public function __construct() {
        $this->passagers = new ArrayCollection();
    }
 
     // Getter and Setter for the Personne relation
     public function getPersonne(): ?Personne
     {
         return $this->personne;
         
     }
 
     public function setPersonne(?Personne $personne): self
     {
         $this->personne = $personne;
         return $this;
     }

    #[ORM\Column]
    private ?int $nbrPlaces = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleA(): ?string
    {
        return $this->villeA;
    }

    public function setVilleA(string $villeA): static
    {
        $this->villeA = $villeA;

        return $this;
    }

    public function getVilleD(): ?string
    {
        return $this->villeD;
    }

    public function setVilleD(string $villeD): static
    {
        $this->villeD = $villeD;

        return $this;
    }

    public function isConducteur(): ?bool
    {
        return $this->conducteur;
    }

    public function setConducteur(bool $conducteur): static
    {
        $this->conducteur = $conducteur;

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

    public function getTrajet(): ?Ville
    {
        return $this->trajet;
    }

    public function setTrajet(?Ville $trajet): static
    {
        $this->trajet = $trajet;

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
