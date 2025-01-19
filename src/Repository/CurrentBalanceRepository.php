<?php

namespace App\Repository;

use App\Entity\CurrentBalance;
use App\Entity\SubAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrentBalance>
 */
class CurrentBalanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrentBalance::class);
    }

    public function updateBalance(SubAccount $subAccount, int $balance): void
    {
        $balanceObj = $this->createQueryBuilder('c')
            ->andWhere('c.subaccount = :val')
            ->setParameter('val', $subAccount)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $balanceObj) {
            $balanceObj = new CurrentBalance();
            $balanceObj->setSubAccount($subAccount);
        }

        $balanceObj->setBalance($balance);

        $em = $this->getEntityManager();
        $em->persist($balanceObj);
        $em->flush();
    }
}
