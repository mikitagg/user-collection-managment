<?php

namespace App\Service;

use App\Entity\Comments;
use App\Entity\Item;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentService
{
    public function initializeComment(?User $user, Item $item): Comments
    {
        $comment = new Comments();
        $comment->setItem($item);
        $comment->setUsername($user);

        return $comment;

    }
}