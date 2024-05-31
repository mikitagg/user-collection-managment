<?php

namespace App\Entity;

use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\OneToMany(targetEntity: ItemsCollection::class, mappedBy: 'user')]
    private Collection $itemCollection;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password;

    #[ORM\Column(type: 'smallint', enumType: UserStatus::class)]
    #[Assert\Type(type: UserStatus::class)]
    private ?UserStatus $status;

    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'username')]
    private Collection $comments;

    public function __construct()
    {
        $this->itemCollection = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->status = UserStatus::Active;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getItemsCollection(): Collection
    {
        return $this->itemCollection;
    }

    public function setItemsCollection(Collection $itemsCollection): static
    {
        $this->itemCollection = $itemsCollection;
        return $this;
    }

    public function addItemsCollection(ItemsCollection $itemsCollection): static
    {
        if (!$this->itemCollection->contains($itemsCollection)) {
            $this->itemCollection->add($itemsCollection);
            $itemsCollection->setUser($this);
        }

        return $this;
    }

    public function removeItemsCollection(ItemsCollection $itemsCollection): static
    {
        if ($this->itemCollection->removeElement($itemsCollection)) {
            // set the owning side to null (unless already changed)
            if ($itemsCollection->getUser() === $this) {
                $itemsCollection->setUser(null);
            }
        }

        return $this;
    }

    public function eraseCredentials(): void
    {

    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUsername($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getUsername() === $this) {
                $comment->setUsername(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?UserStatus
    {
        return $this->status;
    }

    public function setStatus(?UserStatus $status): static
    {
        $this->status = $status;

        return $this;
    }


}
