<?php

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'offres')]
class Offre
{

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

    

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $remuneration = null;

    #[ORM\Column(type: 'integer', nullable: true, name: 'duree_semaines')]
    private ?int $dureeSemaines = null;

    #[ORM\Column(type: 'date_immutable', nullable: true, name: 'date_debut')]
    private ?DateTimeImmutable $dateDebut = null;

   

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
        
    ) {
        $this->titre = $titre;
        $this->description = $description;
        $this->domaine = $domaine;
        $this->localisation = $localisation;
    
        $this->createdAt = new DateTimeImmutable();
    }

  

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


    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

   

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


    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;
        return $this;
    }
}