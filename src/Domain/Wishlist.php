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

    // C'est cette relation qui créera la colonne offre_id
    #[ManyToOne(targetEntity: Offre::class)]
    #[JoinColumn(name: 'offre_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Offre $offre;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(Offre $offre)
    {
        $this->offre = $offre;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int { return $this->id; }
    public function getOffre(): Offre { return $this->offre; }
}