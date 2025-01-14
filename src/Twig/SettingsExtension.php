<?php

namespace App\Twig;

use App\Lib\Manager\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    public function __construct(private readonly SettingsManager $settingsManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('stock_enabled', [$this, 'stockEnabled']),
            new TwigFunction('get_main_account_id', [$this, 'getMainAccountId']),
        ];
    }

    public function stockEnabled(): bool
    {
        return $this->settingsManager->get(SettingsManager::SETTING_STOCK_ACCOUNT_ENABLED, false);
    }

    public function getMainAccountId(): ?int
    {
        $mainSubAccount = $this->settingsManager->get(SettingsManager::SETTING_MAIN_ACCOUNT, null);

        if (null !== $mainSubAccount) {
            return $mainSubAccount->getId();
        } else {
            return null;
        }
    }
}
