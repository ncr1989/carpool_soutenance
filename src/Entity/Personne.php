<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;



#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     
    #[ORM\OneToMany(mappedBy: "personne", targetEntity: Trajet::class)]
    private Collection $trajetsProposes;


    #[ORM\ManyToMany(targetEntity: Trajet::class, inversedBy: 'passagers')]
    #[ORM\JoinTable(name: 'reservations')]
    private Collection $trajetsReserves;

    #[ORM\OneToOne(targetEntity: Voiture::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]  // Ensures voiture_id cannot be NULL
    private ?Voiture $voiture = null;


    public function __construct()
    {
        $this->trajetsProposes = new ArrayCollection();
        $this->trajetsReserves = new ArrayCollection();
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetsProposes(): Collection
    {
        return $this->trajetsProposes;
    }

    public function addTrajetsPropose(Trajet $trajetsPropose): static
    {
        if (!$this->trajetsProposes->contains($trajetsPropose)) {
            $this->trajetsProposes->add($trajetsPropose);
            $trajetsPropose->setPersonne($this);
        }

        return $this;
    }

    public function removeTrajetsPropose(Trajet $trajetsPropose): static
    {
        if ($this->trajetsProposes->removeElement($trajetsPropose)) {
            // set the owning side to null (unless already changed)
            if ($trajetsPropose->getPersonne() === $this) {
                $trajetsPropose->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetsReserves(): Collection
    {
        return $this->trajetsReserves;
    }

    public function addTrajetsReserves(Trajet $trajetsReserf): static
    {
        if (!$this->trajetsReserves->contains($trajetsReserf)) {
            $this->trajetsReserves->add($trajetsReserf);
        }

        return $this;
    }

    public function removeTrajetsReserves(Trajet $trajetsReserf): static
    {
        $this->trajetsReserves->removeElement($trajetsReserf);

        return $this;
    }

    public function getRoles(): array
    {
        // Return the roles (by default, every user should have at least 'ROLE_USER')
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Or return another unique identifier like $this->pseudo
    }

    public function eraseCredentials(): void
    {
        // If you store temporary sensitive data, clear it here (not needed for hashed passwords)
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function addTrajetsReserf(Trajet $trajetsReserf): static
    {
        if (!$this->trajetsReserves->contains($trajetsReserf)) {
            $this->trajetsReserves->add($trajetsReserf);
        }

        return $this;
    }

    public function removeTrajetsReserf(Trajet $trajetsReserf): static
    {
        $this->trajetsReserves->removeElement($trajetsReserf);

        return $this;
    }

    
}
