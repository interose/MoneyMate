<?php

namespace App\Lib\Transaction;

use App\Entity\Category;
use App\Entity\SubAccount;
use Doctrine\ORM\EntityManagerInterface;
use Fhp\Model\StatementOfAccount\Transaction;

class ImportHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CategoryEvaluator $categoryEvaluator,
        private readonly ImportStats $stats,
    ) {
    }

    public function import(array $transactions, SubAccount $subAccount): ImportStats
    {
        $this->stats->iTransactions = count($transactions);

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            if (true !== $transaction->getBooked()) {
                // skip not booked transactions
                continue;
            }

            if (false === $this->isDuplicate($transaction, $subAccount)) {
                ++$this->stats->iNew;
                $category = $this->categoryEvaluator->evaluate($transaction);
                $this->saveTransaction($transaction, $subAccount, $category);
            }
        }

        return $this->stats;
    }

    /**
     * @param Transaction $transaction A single transaction
     *
     * @return bool True if transaction exists, otherwise false
     */
    private function isDuplicate(Transaction $transaction, SubAccount $subAccount): bool
    {
        $checksum = $this->generateChecksum($transaction, $subAccount);
        $entityTransaction = $this->em->getRepository(\App\Entity\Transaction::class)->findOneBy(['checksum' => $checksum]);

        return !is_null($entityTransaction);
    }

    /**
     * @param Transaction $transaction A single transaction
     *
     * @return string A checksum
     */
    private function generateChecksum(Transaction $transaction, SubAccount $subAccount): string
    {
        return md5(
            $transaction->getValutaDate()->format('d.m.Y').$transaction->getAmount().$transaction->getCreditDebit().$transaction->getBookingText().$transaction->getDescription1().$transaction->getDescription2().$transaction->getBankCode().$transaction->getAccountNumber().$transaction->getName().$subAccount->getAccountNumber()
        );
    }

    /**
     * @param Transaction   $transaction A single transaction
     * @param Category|null $category    The category id
     */
    private function saveTransaction(Transaction $transaction, SubAccount $subAccount, ?Category $category = null): void
    {
        $desc = explode('+', $transaction->getDescription1());

        $name = $transaction->getName();
        if ('DEUTSCHE KREDITBANK AG' === $name) {
            $struct = $transaction->getStructuredDescription();

            if (isset($struct['ABWA']) && strlen($struct['ABWA']) > 0) {
                $name = $struct['ABWA'];
            }
        }

        $obj = new \App\Entity\Transaction();
        $obj
            ->setBookingDate($transaction->getBookingDate())
            ->setValutaDate($transaction->getValutaDate())
            ->setAmount(intval($transaction->getAmount() * 100))
            ->setCreditDebit($transaction->getCreditDebit())
            ->setBookingText($transaction->getBookingText())
            ->setDescription($desc[count($desc) - 1])
            ->setDescriptionRaw($transaction->getDescription1())
            ->setBankCode($transaction->getBankCode())
            ->setAccountNumber($transaction->getAccountNumber())
            ->setName($name)
            ->setSubAccount($subAccount)
            ->setChecksum($this->generateChecksum($transaction, $subAccount))
        ;

        if ($category instanceof Category) {
            ++$this->stats->iAssigned;
            $obj->setCategory($category);
        }

        $this->em->persist($obj);
        $this->em->flush();
    }
}
