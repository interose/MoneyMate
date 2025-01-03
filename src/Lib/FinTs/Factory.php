<?php

namespace App\Lib\FinTs;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Factory
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ParameterBagInterface $parameterBag,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getFinTs(Account $account): Base
    {
        list($username, $pin) = $this->accountRepository->getEncrypted($account->getId(), $this->parameterBag->get('encryption_key'));

        $base = new Base(
            $this->requestStack,
            $account->getUrl(),
            $account->getBankCode(),
            $username,
            $pin,
            $this->parameterBag->get('product_name'),
            $this->parameterBag->get('product_version'),
        );

        if (strlen($account->getTanMediaName()) > 0) {
            $base->setTanMedium($account->getTanMediaName());
        }

        if (intval($account->getTanMechanism()) > 0) {
            $base->setTanMode(intval($account->getTanMechanism()));
        }

        return $base;
    }
}
