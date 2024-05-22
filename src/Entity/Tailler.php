<?php

namespace App\Entity;

use App\Repository\TaillerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaillerRepository::class)]
class Tailler
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: 'taillers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(targetEntity: Haie::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Haie $haie = null;

    #[ORM\Column(type: 'decimal', scale: 2)]
    private float $longueur;

    #[ORM\Column(type: 'decimal', scale: 2)]
    private float $hauteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): self
    {
        $this->devis = $devis;

        return $this;
    }

    public function getHaie(): ?Haie
    {
        return $this->haie;
    }

    public function setHaie(?Haie $haie): self
    {
        $this->haie = $haie;

        return $this;
    }

    public function getLongueur(): float
    {
        return $this->longueur;
    }

    public function setLongueur(float $longueur): self
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getHauteur(): float
    {
        return $this->hauteur;
    }

    public function setHauteur(float $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }
}
