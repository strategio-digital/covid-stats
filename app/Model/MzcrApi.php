<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

class MzcrApi
{
    protected Client $client;
    
    protected Cache $cache;
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://onemocneni-aktualne.mzcr.cz/api/v3/',
            'headers' => [
                'accept' => 'application/ld+json'
            ]
        ]);
        
        $path = __DIR__ . '/../../temp/mzcr-api';
        
        if (!file_exists($path)) {
            FileSystem::createDir($path);
        }
        
        $storage = new FileStorage($path);
        $this->cache = new Cache($storage, 'data');
    }
    
    public function ockovaniUmrti(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get($expiration, "ockovani-umrti?page={$page}");
    }
    
    public function ockovaniPozitivni(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get($expiration, "ockovani-pozitivni?page={$page}");
    }
    
    public function ockovaniHospitalizace(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get($expiration, "ockovani-hospitalizace?page={$page}");
    }
    
    public function umrti(\DateTime $expiration, int $page, array $params = []): array
    {
        $query = http_build_query($params);
        return $this->get($expiration, "umrti?page={$page}&{$query}");
    }
    
    public function testyPcrAntigenni(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get($expiration, "testy-pcr-antigenni?page={$page}");
    }
    
    protected function get(\DateTime $expiration, string $url): array
    {
        $fileName = Strings::webalize($url) . '.json';
        
        return $this->cache->load($fileName, function (array|null &$dependencies) use ($expiration, $url) {
            $dependencies[Cache::EXPIRE] = $expiration;
            
            $response = $this->client->get($url . '&apiToken=' . $_ENV['MZCR_API_TOKEN']);
            $responseText = $response->getBody()->getContents();
            
            return json_decode($responseText, true);
        });
    }
}