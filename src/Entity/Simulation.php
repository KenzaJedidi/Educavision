<?php

namespace App\Entity;

use App\Repository\SimulationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimulationRepository::class)]
class Simulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $moyenne = null;

    #[ORM\Column]
    private array $specialites = [];

    #[ORM\Column(nullable: true)]
    private ?array $preferences = null;

    #[ORM\Column]
    private ?\DateTime $dateSimulation = null;

    #[ORM\Column(nullable: true)]
    private ?array $resultats = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoyenne(): ?string
    {
        return $this->moyenne;
    }

    public function setMoyenne(string $moyenne): static
    {
        $this->moyenne = $moyenne;

        return $this;
    }

    public function getSpecialites(): array
    {
        return $this->specialites;
    }

    public function setSpecialites(array $specialites): static
    {
        $this->specialites = $specialites;

        return $this;
    }

    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    public function setPreferences(?array $preferences): static
    {
        $this->preferences = $preferences;

        return $this;
    }

    public function getDateSimulation(): ?\DateTime
    {
        return $this->dateSimulation;
    }

    public function setDateSimulation(\DateTime $dateSimulation): static
    {
        $this->dateSimulation = $dateSimulation;

        return $this;
    }

    public function getResultats(): ?array
    {
        return $this->resultats;
    }

    public function setResultats(?array $resultats): static
    {
        $this->resultats = $resultats;

        return $this;
    }
}