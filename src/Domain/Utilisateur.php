<?php
#[ORM\Entity]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur
{
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_PILOTE   = 'pilote';
    public const ROLE_ETUDIANT = 'etudiant';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $motDePasse; // stocké hashé

    #[ORM\Column(type: 'string', length: 20)]
    private string $role = self::ROLE_ETUDIANT;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 100)]
    private string $Prenom;

    #[ORM\Column(type: 'string', length: 100)]
    private string $Campus;

    #[ORM\Column(type: 'string', length: 100)]
    private string $Promotion;
}