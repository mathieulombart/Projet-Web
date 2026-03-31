<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'candidatures')]
class Candidature
{
    public const STATUT_EN_ATTENTE = 'En attente';
    public const STATUT_ACCEPTEE   = 'Acceptée';
    public const STATUT_REFUSEE    = 'Refusée';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Offre::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Offre $offre;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private Utilisateur $utilisateur;

    #[ORM\Column(type: 'text')]
    private string $motivation;

    #[ORM\Column(type: 'string', length: 255, nullable: true, name: 'cv_path')]
    private ?string $cvPath = null;

    #[ORM\Column(type: 'datetime_immutable', name: 'date_candidature')]
    private DateTimeImmutable $dateCandidature;

    #[ORM\Column(type: 'string', length: 30)]
    private string $statut = self::STATUT_EN_ATTENTE;

    public function __construct(Offre $offre, Utilisateur $utilisateur, string $motivation, ?string $cvPath = null)
    {
        $this->offre = $offre;
        $this->utilisateur = $utilisateur;
        $this->motivation = $motivation;
        $this->cvPath = $cvPath;
        $this->dateCandidature = new DateTimeImmutable();
        
    }

    public function getId(): ?int                           
    { 
        return $this->id;
    }
    public function getOffre(): Offre     
    {                     
        return $this->offre;                    
    }
    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }
    public function getMotivation(): string               
    { 
        return $this->motivation; 
    }
    public function getCvPath(): ?string
    {
        return $this->cvPath;
    }
    public function getStatut(): string                   
    {
        return $this->statut; 
    }
    public function getDateCandidature(): DateTimeImmutable {
        return $this->dateCandidature; 
    }
    public function setStatut(string $statut): void        
    {
        $this->statut = $statut; 
    }
    public function setCvPath(?string $cvPath): void
    {
        $this->cvPath = $cvPath;
    }
    public function setMotivation(string $motivation): void
    {
        $this->motivation = $motivation;
    }
}
