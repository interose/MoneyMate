<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getCategoriesForDropdown(): array
    {
//        $qb = $this->createQueryBuilder('c');
//        $qb->addSelect('g');
//        $qb->leftJoin('c.categoryGroup', 'g');
//        $qb->addOrderBy('ISNULL(g.name)');
//        $qb->addOrderBy('g.name', 'asc');
//        $qb->addOrderBy('c.name', 'asc');



        $sql = <<<SQL
SELECT c.id, c.name AS categoryName, cg.name AS categoryGroupName
FROM category c
LEFT JOIN category_group cg on c.category_group_id = cg.id
ORDER BY ISNULL(cg.name), cg.name ASC, c.name ASC
;
SQL;

        $entityManager = $this->getEntityManager();
        $stmt = $entityManager->getConnection()->prepare($sql);

        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}
