<?php

namespace App\Lib\FinTs;

use Fhp\Model\NoPsd2TanMode;
use Fhp\Model\SEPAAccount;

class BaseWrapper extends Base
{
    /**
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     */
    public function getTanModes(): array
    {
        return $this->finTs->getTanModes();
    }

    /**
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     */
    public function getTanMedia(int $tanMode): array
    {
        return $this->finTs->getTanMedia($tanMode);
    }

    /**
     * @return SEPAAccount[]
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     */
    public function getAllAccounts(?string $tan = null): array
    {
        $action = $this->init($tan);

        return $this->getAccounts($action);
    }
}
