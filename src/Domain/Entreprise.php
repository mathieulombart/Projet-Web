<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'entreprises')]
class Entreprise
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;  

    #[Column(type: 'string', nullable: false)]
    private string $nom;

    #[Column(type: 'string', nullable: true)]
    private ?string $ville = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $secteur = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[Column(type: 'string', length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ManyToOne(targetEntity: Campus::class)]
    #[JoinColumn(name: 'campus_id', nullable: true, onDelete: 'SET NULL')]
    private ?Campus $campus = null;

    public function __construct(string $nom, string $secteur)
    {
        $this->nom      = $nom;
        $this->secteur  = $secteur;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): void
    {
        $this->ville = $ville;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(?string $secteur): void
    {
        $this->secteur = $secteur;
    }

    public function getCampus(): ?Campus { return $this->campus; }
    public function setCampus(?Campus $campus): void { $this->campus = $campus; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): void { $this->telephone = $telephone; }
}