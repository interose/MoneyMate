<?php

namespace App\Repository;

use App\Entity\CategoryGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryGroup>
 */
class CategoryGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryGroup::class);
    }

    public function getByName()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->addSelect('ct');
        $qb->leftJoin('c.categories', 'ct');

        return $qb->getQuery()->getResult();
    }

    public function findBySearch(?string $sort = null, string $direction = 'DESC')
    {
        $qb = $this->createQueryBuilder('c');

        if ($sort) {
            $qb->orderBy('c.'.$sort, $direction);
        }

        return $qb->getQuery()->getResult();
    }
}
