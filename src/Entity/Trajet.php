<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $kms = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_depart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_arrivee = null;

    #[ORM\Column]
    private ?int $place_dispos = null;

    #[ORM\ManyToOne(inversedBy: 'trajets_conducteur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $conducteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $ville_depart = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $ville_arrivee = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voiture $voiture = null;

    #[ORM\ManyToMany(targetEntity: Personne::class, mappedBy: 'trajets_reserves')]
    private Collection $passagers;

    public function __construct()
    {
        $this->passagers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKms(): ?int
    {
        return $this->kms;
    }

    public function setKms(int $kms): self
    {
        $this->kms = $kms;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeuredepart(): ?\DateTimeInterface
    {
        return $this->heure_depart;
    }

    public function setHeuredepart(\DateTimeInterface $heure_depart): self
    {
        $this->heure_depart = $heure_depart;

        return $this;
    }

    public function getHeurearrivee(): ?\DateTimeInterface
    {
        return $this->heure_arrivee;
    }

    public function setHeurearrivee(\DateTimeInterface $heure_arrivee): self
    {
        $this->heure_arrivee = $heure_arrivee;

        return $this;
    }

    public function getPlacedispos(): ?int
    {
        return $this->place_dispos;
    }

    public function setPlacedispos(int $place_dispos): self
    {
        $this->place_dispos = $place_dispos;

        return $this;
    }

    public function getConducteur(): ?Personne
    {
        return $this->conducteur;
    }

    public function setConducteur(?Personne $conducteur): self
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    public function getVilledepart(): ?Ville
    {
        return $this->ville_depart;
    }

    public function setVilledepart(?Ville $ville_depart): self
    {
        $this->ville_depart = $ville_depart;

        return $this;
    }

    public function getVillearrivee(): ?Ville
    {
        return $this->ville_arrivee;
    }

    public function setVillearrivee(?Ville $ville_arrivee): self
    {
        $this->ville_arrivee = $ville_arrivee;

        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;

        return $this;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPassagers(): Collection
    {
        return $this->passagers;
    }

    public function addPassager(Personne $passager): self
    {
        if (!$this->passagers->contains($passager)) {
            $this->passagers->add($passager);
        }

        return $this;
    }

    public function removePassager(Personne $passager): self
    {
        $this->passagers->removeElement($passager);

        return $this;
    }
}