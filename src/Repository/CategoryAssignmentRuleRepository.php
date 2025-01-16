<?php

namespace App\Repository;

use App\Entity\CategoryAssignmentRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryAssignmentRule>
 */
class CategoryAssignmentRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryAssignmentRule::class);
    }

    public function findBySearch(?string $sort = null, string $direction = 'DESC')
    {
        $qb = $this->createQueryBuilder('r');

        if ('category' === $sort) {
            $qb->leftJoin('r.category', 'c');
            $qb->orderBy('c.name', $direction);
        } else {
            $qb->orderBy('r.'.$sort, $direction);
        }

        return $qb->getQuery()->getResult();
    }
}
