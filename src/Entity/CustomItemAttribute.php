<?php

namespace App\Entity;

use App\Enum\CustomAttributeType;
use App\Repository\CustomItemAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomItemAttributeRepository::class)]
class CustomItemAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column(enumType: CustomAttributeType::class)]
    private ?CustomAttributeType $type = null;

    #[ORM\ManyToOne(inversedBy: 'customItemAttributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemsCollection $itemCollection = null;


    /**
     * @var Collection<int, ItemAttributeValue>
     */
    #[ORM\OneToMany(targetEntity: ItemAttributeValue::class, mappedBy: 'customItemAttribute')]
    private Collection $itemAttributeValues;

    public function __construct()
    {
        $this->itemAttributeValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?CustomAttributeType
    {
        return $this->type;
    }

    public function setType(?CustomAttributeType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getItemCollection(): ?ItemsCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(?ItemsCollection $ItemCollection): static
    {
        $this->itemCollection = $ItemCollection;

        return $this;
    }


    /**
     * @return Collection<int, ItemAttributeValue>
     */
    public function getItemAttributeValues(): Collection
    {
        return $this->itemAttributeValues;
    }

    public function addItemAttributeValue(ItemAttributeValue $itemAttributeValue): static
    {
        if (!$this->itemAttributeValues->contains($itemAttributeValue)) {
            $this->itemAttributeValues->add($itemAttributeValue);
            $itemAttributeValue->setCustomItemAttribute($this);
        }

        return $this;
    }

    public function removeItemAttributeValue(ItemAttributeValue $itemAttributeValue): static
    {
        if ($this->itemAttributeValues->removeElement($itemAttributeValue)) {
            // set the owning side to null (unless already changed)
            if ($itemAttributeValue->getCustomItemAttribute() === $this) {
                $itemAttributeValue->setCustomItemAttribute(null);
            }
        }

        return $this;
    }
}
