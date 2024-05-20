<?php

namespace App\Entity;

use App\Repository\ItemAttributeValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemAttributeValueRepository::class)]
class ItemAttributeValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'itemAttributeValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomItemAttribute $customItemAttribute = null;

    #[ORM\ManyToOne(inversedBy: 'itemAttributeValue')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    #[ORM\Column(nullable: false)]
    private mixed $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomItemAttribute(): ?CustomItemAttribute
    {
        return $this->customItemAttribute;
    }

    public function setCustomItemAttribute(?CustomItemAttribute $customItemAttribute): static
    {
        $this->customItemAttribute = $customItemAttribute;

        return $this;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function setName(mixed $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }
}
