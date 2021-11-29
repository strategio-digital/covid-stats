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
    
    protected FileStorage $storage;
    
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
        
        $this->storage = new FileStorage($path);
    }
    
    public function ockovaniDemografie(\DateTime $expiration, int $page, array $params = []) : array
    {
        return $this->get(__FUNCTION__, $expiration, "ockovani-demografie?page={$page}");
    }
    
    public function ockovaniPozitivni(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get(__FUNCTION__, $expiration, "ockovani-pozitivni?page={$page}");
    }
    
    public function ockovaniHospitalizace(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get(__FUNCTION__, $expiration, "ockovani-hospitalizace?page={$page}");
    }
    
    public function ockovaniUmrti(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get(__FUNCTION__, $expiration, "ockovani-umrti?page={$page}");
    }
    
    public function umrti(\DateTime $expiration, int $page, array $params = []): array
    {
        $query = http_build_query($params);
        return $this->get(__FUNCTION__, $expiration, "umrti?page={$page}&{$query}");
    }
    
    public function testyPcrAntigenni(\DateTime $expiration, int $page, array $params = []): array
    {
        return $this->get(__FUNCTION__, $expiration, "testy-pcr-antigenni?page={$page}");
    }
    
    protected function get(string $namespace, \DateTime $expiration, string $url): array
    {
        $key = Strings::webalize($url);
        $cache = new Cache($this->storage, $namespace);
        
        return $cache->load($key, function (array|null &$dependencies) use ($expiration, $url) {
            $dependencies[Cache::EXPIRE] = $expiration;
            
            $response = $this->client->get($url . '&apiToken=' . $_ENV['MZCR_API_TOKEN']);
            $responseText = $response->getBody()->getContents();
            
            return json_decode($responseText, true);
        });
    }
}