<?php

namespace App\Lib\FinTs;

use Fhp\Action\GetSEPAAccounts;
use Fhp\BaseAction;
use Fhp\FinTs;
use Fhp\Model\SEPAAccount;
use Symfony\Component\HttpFoundation\RequestStack;

class Base
{
    protected FinTs $finTs;
    public const SESSION_IDENTIFIER = 'fints';

    private int $tanMode = 0;
    private ?string $tanMedium = null;
    private ?BaseAction $persistedAction = null;

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
        private readonly string $server,
        private readonly string $bankCode,
        private readonly string $username,
        private readonly string $pin,
        private readonly string $productName,
        private readonly string $productVersion,
    ) {
        $this->create();
    }

    /**
     * @param string|null $persistedInstance A previous FinTs instance
     */
    protected function create(?string $persistedInstance = null): void
    {
        $session = $this->requestStack->getSession();

        if ($session->has(self::SESSION_IDENTIFIER)) {
            list($persistedInstance, $persistedAction) = unserialize($session->get(self::SESSION_IDENTIFIER));
            $this->persistedAction = unserialize($persistedAction);
        }

        $options = new \Fhp\Options\FinTsOptions();
        $options->url = $this->server;
        $options->bankCode = $this->bankCode;
        $options->productName = $this->productName;
        $options->productVersion = $this->productVersion;
        $credentials = \Fhp\Options\Credentials::create($this->username, $this->pin);
        $this->finTs = FinTs::new($options, $credentials, $persistedInstance);
    }

    /**
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     * @throws TanRequiredException
     */
    public function handleAction(\stdClass $request): array|bool
    {
        if (!property_exists($request, 'action')) {
            throw new \InvalidArgumentException('Action property missing');
        }

        switch ($request->action) {
            case Action::GetTanModes:
                return $this->finTs->getTanModes();

            case Action::GetTanMedia:
                return $this->finTs->getTanMedia($request->tanMode);

            case Action::GetAllAccounts:
                return $this->getAccounts();

            case Action::CheckDecoupled:
                return $this->finTs->checkDecoupledSubmission($this->persistedAction);

            default:
                throw new \InvalidArgumentException('Unknown action: '.$request->action->value);
        }
    }

    /**
     * @throws \Fhp\CurlException            when the connection fails in a layer below the FinTS protocol
     * @throws \Fhp\Protocol\ServerException when the server responds with a (FinTS-encoded) error message
     * @throws TanRequiredException
     */
    public function login(): void
    {
        $this->finTs->selectTanMode($this->tanMode, $this->tanMedium);

        $action = $this->finTs->login();
        if ($action->needsTan()) {
            $tanRequest = $action->getTanRequest();

            $this->preserveState($action);

            throw new TanRequiredException($tanRequest->getChallenge());
        }
    }

    /**
     * @param BaseAction $action The action which should be saved for the next request
     */
    private function preserveState(BaseAction $action): void
    {
        $persistedAction = serialize($action);
        $persistedFinTs = $this->finTs->persist();

        $session = $this->requestStack->getSession();
        $session->set(self::SESSION_IDENTIFIER, serialize([$persistedFinTs, $persistedAction]));
    }

    /**
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     * @throws TanRequiredException
     * @throws \Exception
     */
    private function handleStrongAuthentication(BaseAction $action): void
    {
        if ($this->finTs->getSelectedTanMode()->isDecoupled()) {
            $tanRequest = $action->getTanRequest();

            $msg = 'The bank requested authentication on another device.';
            if (null !== $tanRequest->getChallenge()) {
                $msg .= "\n".' Instructions: '.$tanRequest->getChallenge();
            }

            if (null !== $tanRequest->getTanMediumName()) {
                $msg .= "\n".'Please check this device: '.$tanRequest->getTanMediumName();
            }

            $this->preserveState($action);

            throw new TanRequiredException($msg);
        } else {
            throw new \Exception('TAN Mode not supported!');
        }
    }

    /**
     * @param BaseAction $action The action which should be executed
     *
     * @throws TanRequiredException          if action needs a tan
     * @throws \Fhp\CurlException            when the connection fails in a layer below the FinTS protocol
     * @throws \Fhp\Protocol\ServerException when the server responds with a (FinTS-encoded) error message
     */
    protected function execute(BaseAction $action): void
    {
        $this->finTs->execute($action);
        if ($action->needsTan()) {
            $this->handleStrongAuthentication($action);
        }
    }

    /**
     * @return SEPAAccount[]
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Exception
     */
    protected function getAccounts(): array
    {
        $action = GetSEPAAccounts::create();
        $this->execute($action);

        $accounts = $action->getAccounts();
        if (0 === count($accounts)) {
            throw new \Exception('No accounts!');
        }

        return $accounts;
    }

    public function setTanMode(int $tanMode): void
    {
        $this->tanMode = $tanMode;
    }

    public function setTanMedium(string $tanMedium): void
    {
        $this->tanMedium = $tanMedium ?? null;
    }
}
