<?php
namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur
{
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_PILOTE   = 'pilote';
    public const ROLE_ETUDIANT = 'etudiant';

    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $identifiant;

    #[ORM\Column(type: 'string', length: 255)]
    private string $motDePasse; // stocké hashé

    #[ORM\Column(type: 'string', length: 20)]
    private string $role = self::ROLE_ETUDIANT;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $campus = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $promotion = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $pilote = null;

    public function getId(): ?int { return $this->id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getIdentifiant(): string { return $this->identifiant; }
    public function setIdentifiant(string $identifiant): void { $this->identifiant = $identifiant; }

    public function getMotDePasse(): string { return $this->motDePasse; }
    public function setMotDePasse(string $motDePasse): void { $this->motDePasse = $motDePasse; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getCampus(): string { return $this->campus; }
    public function setCampus(string $campus): void { $this->campus = $campus; }

    public function getPromotion(): string { return $this->promotion; }
    public function setPromotion(string $promotion): void { $this->promotion = $promotion; }

    public function getPilote(): ?int { return $this->pilote; }
    public function setPilote(?int $pilote): void { $this->pilote = $pilote; }
}