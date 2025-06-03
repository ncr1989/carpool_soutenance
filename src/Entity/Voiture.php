<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VoitureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
#[ApiResource]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modele = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $immatriculation = null;

    #[ORM\OneToOne(mappedBy: 'voiture', targetEntity: Personne::class)]
    private ?Personne $personne = null;

    #[ORM\ManyToOne(targetEntity: Marque::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)] // Ensures voiture must have a marque
    private ?Marque $marque = null;

    // Getter and Setter for Marque
    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;
        return $this;
    }

    // Getter and Setter for Personne
    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(?string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }
}
