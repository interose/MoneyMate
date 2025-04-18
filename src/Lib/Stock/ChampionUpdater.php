<?php

namespace App\Lib\Stock;

use App\Lib\Manager\SettingsManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChampionUpdater
{
    private const CACHE_FILE = 'championsLastUpdated.csv';
    private const HASH_ALGO = 'md5';

    public function __construct(
        #[Autowire('%kernel.cache_dir%/')] private readonly string $cacheDir,
        private readonly HttpClientInterface $client,
        private readonly SettingsManager $settingsManager,
        private readonly ChampionImporter $importer,
    ) {
    }

    public function getLastUpdated(): ?\DateTime
    {
        $lastUpdate = null;
        $file = $this->cacheDir.self::CACHE_FILE;

        if (file_exists($file)) {
            $lastUpdate = new \DateTime();
            $lastUpdate->setTimezone(new \DateTimeZone('Europe/Berlin'));
            $lastUpdate->setTimestamp(filemtime($file));
        }

        return $lastUpdate;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Exception
     */
    public function getUpdate(): bool
    {
        $file = $this->cacheDir.self::CACHE_FILE;

        $localChecksum = $remoteChecksum = null;
        $url = $this->settingsManager->get(SettingsManager::SETTING_STOCK_DIVIDEND_URL);

        if (file_exists($file)) {
            $localChecksum = hash_file(self::HASH_ALGO, $file);
            $remoteChecksum = $this->getRemoteChecksum($url);
        }

        if (null === $localChecksum || null === $remoteChecksum || $localChecksum !== $remoteChecksum) {
            $this->fetchCsv($url);
            $this->importer->doImport($this->cacheDir.self::CACHE_FILE);

            return true;
        } else {
            return false;
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
            'verify_peer' => false,
            'verify_host' => false,
        ]);

        $statusCode = $response->getStatusCode();

        if (200 !== $statusCode) {
            throw new \Exception('Error while fetching csv');
        }

        file_put_contents($this->cacheDir.self::CACHE_FILE, $response->getContent());
    }

    /**
     * @throws \Exception
     */
    private function getRemoteChecksum(string $url): string
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
            'http' => [
                'method' => 'GET',
            ],
        ]);

        $stream = fopen($url, 'rb', false, $context);
        if (!$stream) {
            throw new \Exception("Unable to open remote file: $url");
        }

        $hashContext = hash_init(self::HASH_ALGO);

        while (!feof($stream)) {
            $chunk = fread($stream, 8192); // Read in 8KB chunks
            hash_update($hashContext, $chunk);
        }

        fclose($stream);

        return hash_final($hashContext);
    }
}
