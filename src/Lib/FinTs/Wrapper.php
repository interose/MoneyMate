<?php

namespace App\Lib\FinTs;

use App\Entity\Account;

class Wrapper
{
    public function __construct(
        private readonly Factory $factory
    ) {
    }

    /**
     * @throws \Fhp\CurlException
     * @throws TanRequiredException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTanModeChoices(Account $account): array
    {
        $finTs = $this->factory->getFinTs($account);

        $finTsAction = new \stdClass();
        $finTsAction->action = Action::GetTanModes;

        $tanModeChoices = ['Please select' => ''];
        array_map(function ($item) use (&$tanModeChoices) {
            $tanModeChoices[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'isDecoupled' => $item->isDecoupled(),
                'needsTanMedium' => $item->needsTanMedium(),
            ];
        }, $finTs->handleAction($finTsAction));

        return $tanModeChoices;
    }

    /**
     * @throws \Fhp\CurlException
     * @throws TanRequiredException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTanMediaChoices(Account $account, int $tanMode): array
    {
        $finTs = $this->factory->getFinTs($account);

        $finTsAction = new \stdClass();
        $finTsAction->action = Action::GetTanMedia;
        $finTsAction->tanMode = $tanMode;

        $tanMediaChoices = ['Please select' => ''];
        array_map(function ($item) use (&$tanMediaChoices) {
            $tanMediaChoices[] = [
                'name' => $item->getName(),
                'phoneNumber' => $item->getPhoneNumber(),
            ];
        }, $finTs->handleAction($finTsAction));

        return $tanMediaChoices;
    }

    /**
     * @throws \Fhp\CurlException
     * @throws TanRequiredException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllSubaccounts(Account $account, ): bool|array
    {
        $finTs = $this->factory->getFinTs($account);

        $finTs->login();

        $finTsAction = new \stdClass();
        $finTsAction->action = Action::GetAllAccounts;
        return $finTs->handleAction($finTsAction);
    }
}
