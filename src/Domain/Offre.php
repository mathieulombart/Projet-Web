<?php

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'offres')]
class Offre
{
    public const TYPE_STAGE       = 'stage';
    public const TYPE_ALTERNANCE  = 'alternance';

    public const TYPES = [self::TYPE_STAGE, self::TYPE_ALTERNANCE];

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $titre;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $domaine = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $type = self::TYPE_STAGE;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $remuneration = null;

    #[ORM\Column(type: 'integer', nullable: true, name: 'duree_semaines')]
    private ?int $dureeSemaines = null;

    #[ORM\Column(type: 'date_immutable', nullable: true, name: 'date_debut')]
    private ?DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: 'boolean', name: 'is_active')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Entreprise $entreprise = null;

    public function __construct(
        string $titre,
        ?string $description = null,
        ?string $domaine = null,
        ?string $localisation = null,
        string $type = self::TYPE_STAGE,
    ) {
        $this->titre = $titre;
        $this->description = $description;
        $this->domaine = $domaine;
        $this->localisation = $localisation;
        $this->type = $type;
        $this->createdAt = new DateTimeImmutable();
    }

    // --- Getters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRemuneration(): ?float
    {
        return $this->remuneration;
    }

    public function getDureeSemaines(): ?int
    {
        return $this->dureeSemaines;
    }

    public function getDateDebut(): ?DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    // --- Setters ---

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function setDomaine(?string $domaine): static
    {
        $this->domaine = $domaine;
        return $this;
    }

    public function setLocalisation(?string $localisation): static
    {
        $this->localisation = $localisation;
        return $this;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException("Type invalide : $type");
        }
        $this->type = $type;
        return $this;
    }

    public function setRemuneration(?float $remuneration): static
    {
        $this->remuneration = $remuneration;
        return $this;
    }

    public function setDureeSemaines(?int $dureeSemaines): static
    {
        $this->dureeSemaines = $dureeSemaines;
        return $this;
    }

    public function setDateDebut(?DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;
        return $this;
    }

    // --- Helpers ---

    public function isStage(): bool
    {
        return $this->type === self::TYPE_STAGE;
    }

    public function isAlternance(): bool
    {
        return $this->type === self::TYPE_ALTERNANCE;
    }
}