<?php

namespace App\Repository;

use App\Entity\SubAccount;
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
                $qb->orderBy('t.'.$sort, $direction);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTurnoverByMonth(SubAccount $subAccount, \DateTime $start, \DateTime $end): array
    {
        $con = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
SELECT
    SUM(CASE WHEN t.credit_debit = 'debit' THEN t.amount ELSE 0 END) AS debit,
    SUM(CASE WHEN t.credit_debit = 'credit' THEN t.amount ELSE 0 END) AS credit,
    DATE_FORMAT(t.valuta_date, '%Y-%m') AS month_number,
    DATE_FORMAT(t.valuta_date, '%b') AS month_name,
    DATE_FORMAT(t.valuta_date, '%Y') AS year_number
FROM transaction t
WHERE t.sub_account_id = :subaccount_id
GROUP BY month_number, month_name, year_number
ORDER BY month_number ASC
SQL;
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':subaccount_id', $subAccount->getId());

        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}
