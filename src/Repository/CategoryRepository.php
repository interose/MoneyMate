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

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCategoriesForDropdown(): array
    {
        $sql = <<<SQL
SELECT c.id AS id, c.name AS categoryName, cg.name AS categoryGroupName
FROM category c
LEFT JOIN category_group cg on c.category_group_id = cg.id
ORDER BY ISNULL(cg.name), cg.name ASC, c.name ASC
SQL;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $results = $stmt->executeQuery()->fetchAllAssociative();

        $nested = [];
        foreach ($results as $row) {
            $groupName = $row['categoryGroupName'] ?? 'Uncategorized';

            if (!isset($nested[$groupName])) {
                $nested[$groupName] = [
//                    'groupId' => $row['categoryGroupId'],
                    'groupName' => $groupName,
                    'categories' => [],
                ];
            }

            $nested[$groupName]['categories'][] = [
                'id' => $row['id'],
                'name' => $row['categoryName'],
            ];
        }

        return array_values($nested);
    }

    public function findBySearch(?string $sort = null, string $direction = 'DESC')
    {
        $qb = $this->createQueryBuilder('c');

        if ($sort) {
            if ('group' === $sort) {
                $qb->leftJoin('c.categoryGroup', 'cg');
                $qb->orderBy('cg.name', $direction);
            } else {
                $qb->orderBy('c.'.$sort, $direction);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
