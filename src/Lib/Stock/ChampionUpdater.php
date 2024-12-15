<?php

namespace App\Lib\Stock;

use App\Lib\Manager\SettingsManager;
use App\Repository\SettingRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChampionUpdater
{
    private const CACHE_FILE = 'championsLastUpdated.txt';
    private const DAYS_TO_UPDATE = 1;

    public function __construct(
        #[Autowire('%kernel.cache_dir%/')] private string $cacheDir,
        private readonly HttpClientInterface $client,
        private readonly SettingRepository $settingsRepository,
    ) {
    }

    public function update(): void
    {
        $lastUpdate = null;
        $file = $this->cacheDir.self::CACHE_FILE;
        if (file_exists($file)) {
            try {
                $lastUpdate = new \DateTimeImmutable(file_get_contents($file));
            } catch (\Exception $e) {
                // fail silently, because otherwise we simply fetch the csv
            }
        }

        if (null === $lastUpdate || $lastUpdate->diff(new \DateTimeImmutable())->days > self::DAYS_TO_UPDATE) {
            $url = $this->settingsRepository->findOneBy(['name' => SettingsManager::SETTING_STOCK_DIVIDEND_URL])->getValue();

            if (null !== $url) {
                $this->fetchCsv($url);
            }




            // fetch csv;
//            file_put_contents($file, \now());


        }



    }


    private function fetchCsv(string $url): void
    {
        $this->client->request('GET',$url);

    }
}
