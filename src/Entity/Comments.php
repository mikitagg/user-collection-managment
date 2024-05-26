<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $username = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Item $Item = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $content;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?User
    {
        return $this->username;
    }

    public function setUsername(?User $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->Item;
    }

    public function setItem(?Item $Item): static
    {
        $this->Item = $Item;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }
}
