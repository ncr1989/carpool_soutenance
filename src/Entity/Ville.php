<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $inseeCode = null;

    #[ORM\Column(length: 255)]
    private ?string $cityCode = null;

    #[ORM\Column(length: 10)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 6, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 6, nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 100)]
    private ?string $departmentName = null;

    #[ORM\Column(length: 10)]
    private ?string $departmentNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $regionName = null;

    #[ORM\Column(length: 100)]
    private ?string $regionGeojsonName = null;

    #[ORM\OneToMany(targetEntity: Trajet::class, mappedBy: "villeDepart")]
    private Collection $trajetsDepart;

    #[ORM\OneToMany(targetEntity: Trajet::class, mappedBy: "villeArrivee")]
    private Collection $trajetsArrivee;

    public function __construct()
    {
        $this->trajetsDepart = new ArrayCollection();
        $this->trajetsArrivee = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInseeCode(): ?string
    {
        return $this->inseeCode;
    }

    public function setInseeCode(string $inseeCode): static
    {
        $this->inseeCode = $inseeCode;
        return $this;
    }

    public function getCityCode(): ?string
    {
        return $this->cityCode;
    }

    public function setCityCode(string $cityCode): static
    {
        $this->cityCode = $cityCode;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function setDepartmentName(string $departmentName): static
    {
        $this->departmentName = $departmentName;
        return $this;
    }

    public function getDepartmentNumber(): ?string
    {
        return $this->departmentNumber;
    }

    public function setDepartmentNumber(string $departmentNumber): static
    {
        $this->departmentNumber = $departmentNumber;
        return $this;
    }

    public function getRegionName(): ?string
    {
        return $this->regionName;
    }

    public function setRegionName(string $regionName): static
    {
        $this->regionName = $regionName;
        return $this;
    }

    public function getRegionGeojsonName(): ?string
    {
        return $this->regionGeojsonName;
    }

    public function setRegionGeojsonName(string $regionGeojsonName): static
    {
        $this->regionGeojsonName = $regionGeojsonName;
        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetsDepart(): Collection
    {
        return $this->trajetsDepart;
    }

    public function addTrajetsDepart(Trajet $trajetsDepart): static
    {
        if (!$this->trajetsDepart->contains($trajetsDepart)) {
            $this->trajetsDepart->add($trajetsDepart);
            $trajetsDepart->setVilleDepart($this);
        }

        return $this;
    }

    public function removeTrajetsDepart(Trajet $trajetsDepart): static
    {
        if ($this->trajetsDepart->removeElement($trajetsDepart)) {
            if ($trajetsDepart->getVilleDepart() === $this) {
                $trajetsDepart->setVilleDepart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetsArrivee(): Collection
    {
        return $this->trajetsArrivee;
    }

    public function addTrajetsArrivee(Trajet $trajetsArrivee): static
    {
        if (!$this->trajetsArrivee->contains($trajetsArrivee)) {
            $this->trajetsArrivee->add($trajetsArrivee);
            $trajetsArrivee->setVilleArrivee($this);
        }

        return $this;
    }

    public function removeTrajetsArrivee(Trajet $trajetsArrivee): static
    {
        if ($this->trajetsArrivee->removeElement($trajetsArrivee)) {
            if ($trajetsArrivee->getVilleArrivee() === $this) {
                $trajetsArrivee->setVilleArrivee(null);
            }
        }
        return $this;
    }
}
