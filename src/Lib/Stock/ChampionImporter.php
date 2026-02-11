<?php

namespace App\Lib\Stock;

use App\Entity\StockChampion;
use Doctrine\ORM\EntityManagerInterface;

class ChampionImporter
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws \Exception
     */
    public function doImport(string $file): void
    {
        if (!file_exists($file)) {
            throw new \Exception('File not found');
        }

        $this->truncateTable();

        $file = fopen($file, 'r');
        $lineNumber = 0;
        while ($data = fgetcsv($file, null, ';')) {
            if (0 === $lineNumber++) {
                continue;
            }

            $obj = new StockChampion();
            $obj->setName(iconv('ISO-8859-1', 'UTF-8', $data[0]));
            $obj->setWkn($data[1]);
            $obj->setIndustry(iconv('ISO-8859-1', 'UTF-8', $data[2]));
            $obj->setGeoPak10($this->parsePercentage($data[3]));
            $obj->setProfitConsistency($this->parsePercentage($data[4]));
            $obj->setLossRatio($this->parseFloat($data[5]));
            $obj->setDividendYield($this->parsePercentage($data[6]));
            $obj->setSharePrice($this->parseFloat($data[7]));
            $obj->setGd200($this->parseFloat($data[8]));
            $obj->setTrend(iconv('ISO-8859-1', 'UTF-8', $data[9]));
            $obj->setComment(iconv('ISO-8859-1', 'UTF-8', $data[10]));

            $this->entityManager->persist($obj);
        }

        $this->entityManager->flush();

        fclose($file);
    }

    private function truncateTable(): void
    {
        $classMetadata = $this->entityManager->getClassMetadata(StockChampion::class);
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement($platform->getTruncateTableSQL($classMetadata->getTableName(), true));
    }

    private function parsePercentage(string $value): float
    {
        return (float) str_replace(['%', ','], ['', '.'], $value);
    }

    private function parseFloat(string $value): float
    {
        return (float) str_replace(',', '.', $value);
    }
}
