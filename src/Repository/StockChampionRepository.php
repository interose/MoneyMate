<?php

namespace App\Repository;

use App\Entity\StockChampion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockChampion>
 */
class StockChampionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockChampion::class);
    }

    public function findBySearchAndFilter(string $sort, string $sortDirection, string $query = '', ?array $filter = null): array
    {
        $qb = $this->createQueryBuilder('s');

        if (strlen($query) > 0) {
            $qb
                ->andWhere('s.name LIKE :query OR s.wkn LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }

        if (is_array($filter)) {
            foreach ($filter as $filterkey => $values) {
                $condition = '';
                foreach ($values as $key => $value) {
                    $queryKey = sprintf('%s_%d', $filterkey, $key);
                    $condition .= sprintf(' s.%s = :%s OR', $filterkey, $queryKey);
                    $qb->setParameter($queryKey, $value);
                }

                // delete last OR
                $qb->andWhere(substr($condition, 0, -2));
            }
        }

        return $qb
            ->orderBy('s.'.$sort, $sortDirection)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return StockChampion[] Returns an array of StockChampion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StockChampion
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
