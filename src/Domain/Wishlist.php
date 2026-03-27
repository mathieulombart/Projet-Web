<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'wishlists')]
class Wishlist
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Column(type: 'string', nullable: false)]
    private string $offreId;

    #[Column(type: 'string', nullable: true)]
    private ?string $titre = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $entreprise = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $localisation = null;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(string $offreId)
    {
        $this->offreId   = $offreId;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int { return $this->id; }

    public function getOffreId(): string { return $this->offreId; }
    public function setOffreId(string $offreId): void { $this->offreId = $offreId; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): void { $this->titre = $titre; }

    public function getEntreprise(): ?string { return $this->entreprise; }
    public function setEntreprise(?string $entreprise): void { $this->entreprise = $entreprise; }

    public function getLocalisation(): ?string { return $this->localisation; }
    public function setLocalisation(?string $localisation): void { $this->localisation = $localisation; }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
}