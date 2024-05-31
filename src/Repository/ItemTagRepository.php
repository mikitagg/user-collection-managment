<?php

namespace App\Repository;

use App\Entity\ItemTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemTag>
 */
class ItemTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemTag::class);
    }

        public function findByQuery($query): array
        {
            return $this->createQueryBuilder('t')
                ->where('LOWER(t.name) LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $query . '%')
                ->getQuery()
                ->getResult()
                ;
        }
}
