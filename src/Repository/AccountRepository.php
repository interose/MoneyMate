<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveEncrypted(int $id, string $username, string $password, string $key): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
UPDATE account SET username = AES_ENCRYPT(:username, :key), password = AES_ENCRYPT(:password, :key) WHERE id = :id
SQL;

        $stmt = $connection->prepare($sql);
        $stmt->executeStatement([
            'id' => $id,
            'username' => $username,
            'password' => $password,
            'key' => $key,
        ]);
    }

    /**
     * @return false|array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getEncrypted(int $id, string $key): array|bool
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
SELECT AES_DECRYPT(username, :key) AS username, AES_DECRYPT(password, :key) AS password FROM account WHERE id = :id
SQL;

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('key', $key);
        $result = $stmt->executeQuery();

        return $result->fetchNumeric();
    }
}
