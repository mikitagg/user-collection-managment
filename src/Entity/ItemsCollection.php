<?php

namespace App\Entity;

use App\Repository\ItemsCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemsCollectionRepository::class)]
class ItemsCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'itemCollection', orphanRemoval: true)]
    private Collection $items;

    /**
     * @var Collection<int, CustomItemAttribute>
     */
    #[ORM\OneToMany(targetEntity: CustomItemAttribute::class, mappedBy: 'itemCollection', cascade: ["persist"], orphanRemoval: true)]
    private Collection $customItemAttributes;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'user')]
    private ?User $user;

    #[ORM\ManyToOne(inversedBy: 'itemCollection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CollectionCategory $collectionCategory;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->customItemAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setItemCollection($this);
        }
        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItemCollection() === $this) {
                $item->setItemCollection(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, CustomItemAttribute>
     */
    public function getCustomItemAttributes(): Collection
    {
        return $this->customItemAttributes;
    }

    public function addCustomItemAttribute(CustomItemAttribute $customItemAttribute): static
    {
        if (!$this->customItemAttributes->contains($customItemAttribute)) {
            $this->customItemAttributes->add($customItemAttribute);
            $customItemAttribute->setItemCollection($this);
        }
        return $this;
    }

    public function removeCustomItemAttribute(CustomItemAttribute $customItemAttribute): static
    {
        if ($this->customItemAttributes->removeElement($customItemAttribute)) {
            if ($customItemAttribute->getItemCollection() === $this) {
                $customItemAttribute->setItemCollection(null);
            }
        }
        return $this;
    }

public function getCollectionCategory(): ?CollectionCategory
{
    return $this->collectionCategory;
}

public function setCollectionCategory(?CollectionCategory $collectionCategory): static
{
    $this->collectionCategory = $collectionCategory;

    return $this;
}

}
