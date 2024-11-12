<?php

namespace App\Lib;

use App\Entity\Account;
use App\Entity\SubAccount;
use Doctrine\ORM\EntityManagerInterface;
use Fhp\Model\SEPAAccount;

class SubAccountUpdater
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @param Account $account
     * @param SEPAAccount[] $subAccounts
     *
     * @return void
     */
    public function createOrUpdate(Account $account, array $subAccounts): void
    {
        $existingSubAccounts = $account->getSubAccounts();

        if ($existingSubAccounts->count() > 0) {
            $this->update($account, $subAccounts);
        } else {
            $this->create($account, $subAccounts);
        }
    }

    /**
     * @param Account $account
     * @param SEPAAccount[] $subAccounts
     *
     * @return void
     */
    private function create(Account $account, array $subAccounts): void
    {
        foreach ($subAccounts as $subAccount) {
            $this->persistSubAccount($account, $subAccount);
        }
    }

    /**
     * @param Account $account
     * @param SEPAAccount[] $subAccounts
     *
     * @return void
     */
    private function update(Account $account, array $subAccounts): void
    {
        foreach ($subAccounts as $subAccount) {
            $iban = $subAccount->getIban();
            $found = false;

            /** @var SubAccount $objSubAccount */
            foreach ($account->getSubAccounts() as $objSubAccount) {
                if ($objSubAccount->getIban() === $iban) {
                    $found = true;
                }
            }

            if (!$found) {
                $this->persistSubAccount($account, $subAccount);
            }
        }
    }

    /**
     * @param Account $account
     * @param SEPAAccount $subAccount
     *
     * @return void
     */
    private function persistSubAccount(Account $account, SEPAAccount $subAccount): void
    {
        $objSubAccount = new SubAccount();
        $objSubAccount->setAccount($account);
        $objSubAccount->setAccountNumber($subAccount->getAccountNumber());
        $objSubAccount->setIban($subAccount->getIban());
        $objSubAccount->setEnabled(true);

        $this->em->persist($objSubAccount);
        $this->em->flush();
    }
}
