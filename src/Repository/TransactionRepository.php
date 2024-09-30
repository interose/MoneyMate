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

    public function findBySearch(int $month, int $year, ?string $sort = null, string $direction = 'DESC', ?string $query = null): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->addSelect('c');
        $qb->leftJoin('t.category', 'c');

        if (null !== $query && strlen($query) > 0) {
            $qb->orWhere($qb->expr()->like('t.name', ':name'))->setParameter('name', '%'.$query.'%');
            $qb->orWhere($qb->expr()->like('t.descriptionRaw', ':name'))->setParameter('name', '%'.$query.'%');
        } else {
            $qb->andWhere('MONTH(t.valutaDate) = :month')->setParameter('month', $month);
            $qb->andWhere('YEAR(t.valutaDate) = :year')->setParameter('year', $year);
        }

        if ($sort) {
            if ('category' === $sort) {
                $qb->orderBy('c.name', $direction);
            } else {
                $qb->orderBy('t.' . $sort, $direction);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
