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

    #[ORM\Column(type: 'text')]
    private string $motivation;

    #[ORM\Column(type: 'datetime_immutable', name: 'date_candidature')]
    private DateTimeImmutable $dateCandidature;

    #[ORM\Column(type: 'string', length: 30)]
    private string $statut = self::STATUT_EN_ATTENTE;

    public function __construct(Offre $offre, string $motivation)
    {
        $this->offre           = $offre;
        $this->motivation      = $motivation;
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
    public function getMotivation(): string               
    { 
        return $this->motivation; 
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
}
