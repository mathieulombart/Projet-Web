<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'campus')]
class Campus{

    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $ville;

    public function getId(): ?int { return $this->id; }

    public function getCampus(): string { return $this->ville; }
    public function setCampus(string $ville): void { $this->ville = $ville; }
}