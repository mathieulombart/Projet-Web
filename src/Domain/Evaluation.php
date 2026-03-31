<?php

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'evaluations')]
class Evaluation
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $note;

    #[ORM\Column(type: 'text')]
    private string $commentaire;

    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Entreprise $entreprise;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Utilisateur $utilisateur;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(
        int $note,
        string $commentaire,
        Entreprise $entreprise,
        Utilisateur $utilisateur
    ) {
        $this->note = $note;
        $this->commentaire = $commentaire;
        $this->entreprise = $entreprise;
        $this->utilisateur = $utilisateur;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function getEntreprise(): Entreprise
    {
        return $this->entreprise;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}