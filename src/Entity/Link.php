<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'links')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'links')]
    private ?Tableau $tableau = null;

    #[ORM\Column(length: 255)]
    private ?string $Ownership = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTableau(): ?Tableau
    {
        return $this->tableau;
    }

    public function setTableau(?Tableau $tableau): static
    {
        $this->tableau = $tableau;

        return $this;
    }

    public function getOwnership(): ?string
    {
        return $this->Ownership;
    }

    public function setOwnership(string $Ownership): static
    {
        $this->Ownership = $Ownership;

        return $this;
    }
}
