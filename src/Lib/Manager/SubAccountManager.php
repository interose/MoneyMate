<?php

namespace App\Lib\Manager;

use App\Entity\Account;
use App\Entity\SubAccount;
use Doctrine\ORM\EntityManagerInterface;
use Fhp\Model\SEPAAccount;

final class SubAccountManager
{
    public function __construct(private readonly EntityManagerInterface $em) {

    }

    /**
     * @param Account $account
     * @param SEPAAccount[] $subAccounts
     * @return array
     */
    public function createOrUpdate(Account $account, array $subAccounts): array
    {
        $existingSubAccounts = $account->getSubAccounts();

        if ($existingSubAccounts->count() > 0) {
            return $this->update($account, $subAccounts);
        } else {
            return $this->create($account, $subAccounts);
        }
    }

    /**
     * @param Account $account
     * @param array $sepaAccounts
     * @return SubAccount[]
     */
    private function create(Account $account, array $sepaAccounts): array
    {
        $subAccounts = [];

        foreach ($sepaAccounts as $sepaAccount) {
            $subAccounts[] = $this->persistSubAccount($account, $sepaAccount);
        }

        return $subAccounts;
    }

    /**
     * @param Account $account
     * @param SEPAAccount[] $subAccounts
     * @return void
     */
    private function update(Account $account, array $sepaAccounts): array
    {
        $subAccounts = [];

        foreach ($sepaAccounts as $sepaAccount) {
            $iban = $sepaAccount->getIban();
            $found = false;

            /** @var SubAccount $objSubAccount */
            foreach ($account->getSubAccounts() as $objSubAccount) {
                if ($objSubAccount->getIban() === $iban) {
                    $found = true;
                }
            }

            if (!$found) {
                $subAccounts[] = $this->persistSubAccount($account, $sepaAccount);
            } else {
                $subAccounts[] = $objSubAccount;
            }
        }

        return $subAccounts;
    }

    private function persistSubAccount(Account $account, SEPAAccount $sepaAccount): SubAccount
    {
        $objSubAccount = new SubAccount();
        $objSubAccount->setAccount($account);
        $objSubAccount->setAccountNumber($sepaAccount->getAccountNumber());
        $objSubAccount->setIban($sepaAccount->getIban());
        $objSubAccount->setEnabled(true);

        $this->em->persist($objSubAccount);
        $this->em->flush();

        return $objSubAccount;
    }
}
