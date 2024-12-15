<?php

namespace App\Lib\Manager;

use App\Entity\Setting;
use App\Entity\SubAccount;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;

class SettingsManager
{
    public const SETTING_MAIN_ACCOUNT = 'mainAccount';
    public const SETTING_STOCK_ACCOUNT_ENABLED = 'boerseAccountEnabled';
    public const SETTING_STOCK_PUBLISHER_USER = 'boersePublisherUser';
    public const SETTING_STOCK_PUBLISHER_PW = 'boersePublisherPw';
    public const SETTING_STOCK_DIVIDEND_URL = 'boerseDividendUrl';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SettingRepository $repository,
        private array $globalSettings = [],
    ) {
    }

    public function get(string $name, $default = null)
    {
        $value = $this->globalSettings[$name] ?? null;

        return null === $value ? $default : $value;
    }

    public function all(): array
    {
        $this->loadSettings();

        return $this->globalSettings;
    }

    /**
     * Sets settings' values from associative name-value array.
     */
    public function setMany(array $settings): void
    {
        foreach ($settings as $name => $value) {
            $this->setWithoutFlush($name, $value);
        }

        $this->flush(array_keys($settings));
    }

    private function setWithoutFlush(string $name, $value): void
    {
        $this->globalSettings[$name] = $value;
    }

    private function flush(array $names): void
    {
        $settings = $this->repository->findBy(['name' => $names]);

        foreach ($names as $name) {
            $value = $this->get($name, '');

            $setting = $this->findSettingByName($settings, $name);

            // if the setting does not exist in the DB, create it
            if (!$setting) {
                $setting = new Setting();
                $setting->setName($name);

                $this->entityManager->persist($setting);
            }

            switch ($name) {
                case self::SETTING_MAIN_ACCOUNT:
                    $value = (string) $value->getId();
                    break;
            }

            $setting->setValue($value);
        }

        $this->entityManager->flush();
    }

    private function findSettingByName(array $haystack, string $needle): ?Setting
    {
        foreach ($haystack as $setting) {
            if ($setting->getName() === $needle) {
                return $setting;
            }
        }

        return null;
    }

    private function loadSettings(): void
    {
        if (0 === count($this->globalSettings)) {
            $this->globalSettings = $this->getSettingsFromRepository();
        }
    }

    private function getSettingsFromRepository(): array
    {
        $settings = [];

        foreach ($this->repository->findAll() as $setting) {
            $name = $setting->getName();

            switch ($name) {
                case self::SETTING_MAIN_ACCOUNT:
                    $value = $this->entityManager->getRepository(SubAccount::class)->findOneBy(['id' => $setting->getValue()]);
                    break;

                case self::SETTING_STOCK_ACCOUNT_ENABLED:
                    $value = $setting->getValue() === '1';
                    break;

                default:
                    $value = $setting->getValue();
            }

            $settings[$name] = $value;
        }

        return $settings;
    }
}
