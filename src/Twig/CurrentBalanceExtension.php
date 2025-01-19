<?php

namespace App\Twig;

use App\Repository\SubAccountRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentBalanceExtension extends AbstractExtension
{
    public function __construct(private readonly SubAccountRepository $repository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_balance', [$this, 'getCurrentBalance']),
        ];
    }

    public function getCurrentBalance(int $subAccountId): ?float
    {
        $subAccount = $this->repository->findOneById($subAccountId);
        if (null === $subAccount) {
            return null;
        }

        return !is_null($subAccount->getCurrentBalance()) ? $subAccount->getCurrentBalance()->getBalance() / 100 : 0;
    }
}
