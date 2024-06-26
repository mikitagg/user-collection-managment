<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findLastFive()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findItemAttributes($itemId)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.itemAttributeValue', 'v')
            ->leftJoin('v.customItemAttribute', 'c')
            ->addSelect('v')
            ->addSelect('c')
            ->where('i.id = :id')
            ->setParameter('id', $itemId)
            ->getQuery()
            ->getOneOrNullResult();

    }
}
