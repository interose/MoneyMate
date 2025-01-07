<?php

namespace App\Lib\Stock;

use App\Lib\Manager\SettingsManager;
use App\Repository\SettingRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChampionUpdater
{
    private const CACHE_FILE = 'championsLastUpdated.csv';
    private const DAYS_TO_UPDATE = 7;

    public function __construct(
        #[Autowire('%kernel.cache_dir%/')] private string $cacheDir,
        private readonly HttpClientInterface $client,
        private readonly SettingRepository $settingsRepository,
        private readonly ChampionImporter $importer,
    ) {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Exception
     */
    public function checkUpdate(): void
    {
        $lastUpdate = null;
        $file = $this->cacheDir.self::CACHE_FILE;
        if (file_exists($file)) {
            $lastUpdate = new \DateTimeImmutable();
            $lastUpdate->setTimestamp(filemtime($file));
        }

        if (null === $lastUpdate || $lastUpdate->diff(new \DateTimeImmutable())->days > self::DAYS_TO_UPDATE) {
            $url = $this->settingsRepository->findOneBy(['name' => SettingsManager::SETTING_STOCK_DIVIDEND_URL])->getValue();

            if (null !== $url) {
                $this->fetchCsv($url);

                $this->importer->doImport($this->cacheDir.self::CACHE_FILE);
            }
        }
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    private function fetchCsv(string $url): void
    {
        $response = $this->client->request('GET', $url, [
            'verify_peer' => 0,
            'verify_host' => 0,
        ]);

        $statusCode = $response->getStatusCode();

        if (200 !== $statusCode) {
            throw new \Exception('Error while fetching csv');
        }

        file_put_contents($this->cacheDir.self::CACHE_FILE, $response->getContent());
    }
}
