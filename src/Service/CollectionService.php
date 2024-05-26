<?php

namespace App\Service;

use App\Entity\ItemsCollection;
use Doctrine\ORM\EntityManagerInterface;

class CollectionService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {}


    public function getShowingCollection($id, )
    {
        return $this->entityManager->getRepository(ItemsCollection::class)->findBy(['itemCollection' => $id], ['sortField' => 'sortDirection']);

    }
}