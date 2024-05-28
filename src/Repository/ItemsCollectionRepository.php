<?php

namespace App\Repository;

use App\Entity\ItemsCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemsCollection>
 */
class ItemsCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemsCollection::class);
    }

    public function findWithCustomAttributes($id)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.customItemAttributes', 'c')
            ->addSelect('c')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getItemCollectionWithCategories($user)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.collectionCategory', 'c')
            ->leftJoin('i.user', 'u')
            ->addSelect('u')
            ->addSelect('c')
            ->where('i.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

    }

    public function findTopFiveCollections()
    {
        return $this->createQueryBuilder('ic')
            ->leftJoin('ic.items', 'i') // Assuming the association is named 'items'
            ->addSelect('COUNT(i) as itemCount')
            ->groupBy('ic.id')
            ->orderBy('itemCount', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getItemCollectionWithAuthorAndCategory()
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.collectionCategory', 'c')
            ->leftJoin('i.user', 'u')
            ->addSelect('u')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return ItemsCollection[] Returns an array of ItemsCollection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ItemsCollection
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
