<?php

namespace App\Lib\FinTs;

use Fhp\BaseAction;
use Fhp\FinTs;
use Fhp\Action\GetSEPAAccounts;
use Fhp\Model\NoPsd2TanMode;
use Fhp\Model\SEPAAccount;
use Symfony\Component\HttpFoundation\RequestStack;

class Base
{
    protected FinTs $finTs;
    public const SESSION_IDENTIFIER = 'fints';

    private int $tanMode = 0;
    private string $tanMedium = '';

    /**
     * FinTsBase constructor.
     *
     * @param RequestStack $requestStack   The session interface for storing a FinTS instance across php sessions
     * @param string       $server         the URL where the bank server can be reached
     * @param string       $bankCode       the bank code (Bankleitzahl) of the bank
     * @param string       $username       The username
     * @param string       $pin            this is the PIN used for login
     * @param string       $productName    Identifies the product (i.e. the application in which the phpFinTS library is being used).
     * @param string       $productVersion the product version, which can be an arbitrary string, though if your the application displays a version number somewhere on its own user interface, it should match that
     */
    public function __construct(
        protected RequestStack $requestStack,
        private string $server,
        private string $bankCode,
        private string $username,
        private string $pin,
        private string $productName,
        private string $productVersion
    ) {
        $this->create();
    }

    /**
     * @param string|null $persistedInstance A previous FinTs instance
     */
    protected function create(?string $persistedInstance = null): void
    {
        $options = new \Fhp\Options\FinTsOptions();
        $options->url = $this->server;
        $options->bankCode = $this->bankCode;
        $options->productName = $this->productName;
        $options->productVersion = $this->productVersion;
        $credentials = \Fhp\Options\Credentials::create($this->username, $this->pin);
        $this->finTs = FinTs::new($options, $credentials, $persistedInstance);

    }

    /**
     * @param string|null $tan
     *
     * @return BaseAction|null
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     */
    protected function init(?string $tan = ''): ?BaseAction
    {
        $action = null;

        if (0 === strlen($tan)) {
            $this->login();
        } else {
            $action = $this->submitTan($tan);
        }

        return $action;
    }

    /**
     * @throws TanRequiredException          if action needs a tan
     * @throws \Fhp\CurlException            when the connection fails in a layer below the FinTS protocol
     * @throws \Fhp\Protocol\ServerException when the server responds with a (FinTS-encoded) error message
     */
    protected function login(): void
    {
        $this->finTs->selectTanMode($this->tanMode, $this->tanMedium);

        $action = $this->finTs->login();
        if ($action->needsTan()) {
            $this->preserveState($action);
        }
    }

    /**
     * @param string $tan The tan
     *
     * @return BaseAction Instance of BaseAction
     *
     * @throws \Exception If a previous session could not be created from the session
     */
    protected function submitTan(string $tan): BaseAction
    {
        $session = $this->requestStack->getSession();
        if (!$session->has(self::SESSION_IDENTIFIER)) {
            throw new \Exception('Could not restore state!');
        }

        $state = $session->get(self::SESSION_IDENTIFIER);
        if (is_null($state)) {
            throw new \Exception('Could not restore state! Sessionstate is empty!');
        }

        list($persistedInstance, $persistedAction) = unserialize($state);

        $this->create($persistedInstance);

        $action = unserialize($persistedAction);

        $this->finTs->submitTan($action, $tan);

        return $action;
    }

    /**
     * @param BaseAction $action The action which should be saved for the next request
     *
     * @throws TanRequiredException if action needs a tan
     */
    private function preserveState(BaseAction $action)
    {
        $persistedAction = serialize($action);
        $persistedFinTs = $this->finTs->persist();

        $session = $this->requestStack->getSession();
        $session->set(self::SESSION_IDENTIFIER, serialize([$persistedFinTs, $persistedAction]));

        throw new TanRequiredException();
    }

    /**
     * @param BaseAction $action The action which should be executed
     *
     * @throws TanRequiredException          if action needs a tan
     * @throws \Fhp\CurlException            when the connection fails in a layer below the FinTS protocol
     * @throws \Fhp\Protocol\ServerException when the server responds with a (FinTS-encoded) error message
     */
    protected function execute(BaseAction $action)
    {
        $this->finTs->execute($action);
        if ($action->needsTan()) {
            $this->preserveState($action);
        }
    }

    /**
     * @param BaseAction|null $action
     *
     * @return SEPAAccount[]
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Exception
     */
    protected function getAccounts(?BaseAction $action = null): array
    {
        /* no action from the previous session, start from the beginning */
        if (is_null($action)) {
            $action = GetSEPAAccounts::create();
            $this->execute($action);
        }

        if ($action instanceof GetSEPAAccounts) {
            $accounts = $action->getAccounts();
            if (!is_array($accounts) || 0 === count($accounts)) {
                throw new \Exception('No accounts!');
            }
        } else {
            throw new \Exception(sprintf('Invalid action: %s', get_class($action)));
        }

        return $accounts;
    }

    /**
     * @param string $tanMode
     */
    public function setTanMode(int $tanMode): void
    {
        $this->tanMode = $tanMode;
    }

    /**
     * @param string $tanMedium
     */
    public function setTanMedium(string $tanMedium): void
    {
        $this->tanMedium = $tanMedium;
    }
}
