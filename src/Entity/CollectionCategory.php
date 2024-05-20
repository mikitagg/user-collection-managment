<?php

namespace App\Entity;

use App\Repository\CollectionCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionCategoryRepository::class)]
class CollectionCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $name = null;

    /**
     * @var Collection<int, ItemsCollection>
     */
    #[ORM\OneToMany(targetEntity: ItemsCollection::class, mappedBy: 'collectionCategory')]
    private Collection $itemCollection;

    public function __construct()
    {
        $this->itemCollection = new ArrayCollection();
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

    /**
     * @return Collection<int, ItemsCollection>
     */
    public function getItemsCollection(): Collection
    {
        return $this->itemCollection;
    }

    public function addItemsCollection(ItemsCollection $itemsCollection): static
    {
        if (!$this->itemCollection->contains($itemsCollection)) {
            $this->itemCollection->add($itemsCollection);
            $itemsCollection->setCollectionCategory($this);
        }

        return $this;
    }

    public function removeItemsCollection(ItemsCollection $itemsCollection): static
    {
        if ($this->itemCollection->removeElement($itemsCollection)) {
            // set the owning side to null (unless already changed)
            if ($itemsCollection->getCollectionCategory() === $this) {
                $itemsCollection->setCollectionCategory(null);
            }
        }

        return $this;
    }
}
