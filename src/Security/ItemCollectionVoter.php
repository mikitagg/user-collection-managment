<?php

namespace App\Security;

use App\Controller\CollectionController;
use App\Entity\ItemsCollection;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ItemCollectionVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';


    public function __construct(
        private Security $security,
    ) {
    }

    protected function voteOnAttribute($attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $collection = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($collection, $user),
            self::EDIT => $this->canEdit($collection, $user),
            default => throw new \LogicException('This code should not be reached!')
        };


    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof ItemsCollection) {
            return false;
        }

        return true;
    }


    private function canView(ItemsCollection $collection, User $user): bool
    {
        if ($this->canEdit($collection, $user )) {
            return true;
        }
        return $user === $collection->getUser();
    }

    private function canEdit(ItemsCollection $collection, User $user): bool
    {
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return $user === $collection->getUser() ;
    }
}