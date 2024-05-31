<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comments>
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function findCommentsByItemId($itemId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.Item = :itemId')
            ->setParameter('itemId', $itemId)
            ->getQuery()
            ->getResult();
    }
}
