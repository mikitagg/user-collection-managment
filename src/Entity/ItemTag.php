<?php

namespace App\Entity;

use App\Repository\ItemTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemTagRepository::class)]
class ItemTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'itemTags')]
    private Collection $Item;

    public function __construct()
    {
        $this->Item = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItem(): Collection
    {
        return $this->Item;
    }

    public function addItem(Item $item): static
    {
        if (!$this->Item->contains($item)) {
            $this->Item->add($item);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        $this->Item->removeElement($item);

        return $this;
    }
}
