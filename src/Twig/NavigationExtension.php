<?php

namespace App\Twig;

use App\Lib\Manager\SettingsManager;
use Twig\Extension\AbstractExtension;

class NavigationExtension extends AbstractExtension
{
    public function __construct(private readonly SettingsManager $settingsManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('stock_enabled', [$this, 'stockEnabled']),
        ];
    }

    public function stockEnabled(): bool
    {
        return $this->settingsManager->get(SettingsManager::SETTING_STOCK_ACCOUNT_ENABLED, false);
    }
}
