<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findBySearch(int $month, int $year, ?string $sort = null, string $direction = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('t');

        $qb->andWhere('MONTH(t.valutaDate) = :month')->setParameter('month', $month);
        $qb->andWhere('YEAR(t.valutaDate) = :year')->setParameter('year', $year);

        if ($sort) {

            if ('category' === $sort) {
                $qb
                    ->leftJoin('t.category', 'c')
                    ->orderBy('c.name', $direction);
            } else {
                $qb->orderBy('t.' . $sort, $direction);
            }
        }

        return $qb->getQuery()->getResult();

    }

    //    /**
    //     * @return Transaction[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
