<?php
declare(strict_types=1);
namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\{Entity, Table, Id, Column, GeneratedValue, ManyToOne, JoinColumn};

#[Entity, Table(name: 'wishlists')]
class Wishlist
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Offre::class)]
    #[JoinColumn(name: 'offre_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Offre $offre;

    #[ManyToOne(targetEntity: Utilisateur::class)]
    #[JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Utilisateur $utilisateur;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(Offre $offre, Utilisateur $utilisateur)
    {
        $this->offre       = $offre;
        $this->utilisateur = $utilisateur;
        $this->createdAt   = new DateTimeImmutable('now');
    }

    public function getId(): ?int { return $this->id; }
    public function getOffre(): Offre { return $this->offre; }
    public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
}