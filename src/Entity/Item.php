<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemsCollection $itemCollection = null;


    #[ORM\OneToMany(targetEntity: ItemAttributeValue::class, mappedBy: 'item', cascade: ['persist'], orphanRemoval: true)]
    private Collection $itemAttributeValue;

    /**
     * @var Collection<int, ItemTag>
     */
    #[ORM\ManyToMany(targetEntity: ItemTag::class, mappedBy: 'item', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $itemTags;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'Item', orphanRemoval: true)]
    private Collection $comments;


    public function __construct()
    {
        $this->itemAttributeValue = new ArrayCollection();
        $this->itemTags = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getItemCollection(): ?ItemsCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(?ItemsCollection $itemCollection): void
    {
        $this->itemCollection = $itemCollection;

    }

    /**
     * @return Collection<int, ItemAttributeValue>
     */
    public function getItemAttributeValue(): Collection
    {
        return $this->itemAttributeValue;
    }

    public function setItemAttributeValue(Collection $itemAttributeValue): static
    {
        $this->itemAttributeValue = $itemAttributeValue;
        return $this;
    }

    public function addItemAttributeValue(ItemAttributeValue $itemAttributeValue): static
    {
        if (!$this->itemAttributeValue->contains($itemAttributeValue)) {
            $this->itemAttributeValue->add($itemAttributeValue);
            $itemAttributeValue->setItem($this);
        }

        return $this;
    }

    public function removeItemAttributeValue(ItemAttributeValue $itemAttributeValue): static
    {
        if ($this->itemAttributeValue->removeElement($itemAttributeValue)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeValue->getItem() === $this) {
                $itemAttributeValue->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemTag>
     */
    public function getItemTags(): Collection
    {
        return $this->itemTags;
    }

    public function addItemTag(ItemTag $itemTag): static
    {
        if (!$this->itemTags->contains($itemTag)) {
            $this->itemTags->add($itemTag);
            $itemTag->addItem($this);
        }

        return $this;
    }

    public function removeItemTag(ItemTag $itemTag): static
    {
        if ($this->itemTags->removeElement($itemTag)) {
            $itemTag->removeItem($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setItem($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getItem() === $this) {
                $comment->setItem(null);
            }
        }

        return $this;
    }

}
