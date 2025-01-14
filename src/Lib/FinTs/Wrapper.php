<?php

namespace App\Lib\FinTs;

use App\Entity\Account;
use Fhp\Model\SEPAAccount;

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

        return array_map(function ($item) use (&$tanModeChoices) {
            return [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'isDecoupled' => $item->isDecoupled(),
                'needsTanMedium' => $item->needsTanMedium(),
            ];
        }, $finTs->handleAction($finTsAction));
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
    public function getAllSubaccounts(Account $account, ?string $action = null): bool|array
    {
        $finTs = $this->factory->getFinTs($account);

        if (is_null($action)) {
            $finTs->login();
        } else {
            $finTsAction = new \stdClass();
            $finTsAction->action = Action::CheckDecoupled;
            if (true !== $finTs->handleAction($finTsAction)) {
                throw new TanRequiredException();
            }
        }

        $finTsAction = new \stdClass();
        $finTsAction->action = Action::GetAllAccounts;

        return $finTs->handleAction($finTsAction);
    }

    /**
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getStatements(Account $account, SEPAAccount $sepaAccount, \DateTime $from, \DateTime $to, ?string $action = null): bool|array
    {
        $finTs = $this->factory->getFinTs($account);

        if (is_null($action)) {
            $finTs->login();
        } else {
            $finTsAction = new \stdClass();
            $finTsAction->action = Action::CheckDecoupled;
            if (true !== $finTs->handleAction($finTsAction)) {
                throw new TanRequiredException();
            }
        }

        $finTsAction = new \stdClass();
        $finTsAction->action = Action::GetStatementOfAccount;
        $finTsAction->account = $sepaAccount;
        $finTsAction->getFrom = $from;
        $finTsAction->getTo = $to;

        return $finTs->handleAction($finTsAction);
    }
}
