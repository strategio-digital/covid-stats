<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

class MzcrApi
{
    const CACHE_PATH = __DIR__ . '/../../temp/mzcr-api';
    
    protected Client $client;
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://onemocneni-aktualne.mzcr.cz/api/v3/',
            'headers' => [
                'accept' => 'application/ld+json'
            ]
        ]);
    }
    
    public function ockovaniUmrti(int $page, array $params = []): array
    {
        return $this->get("ockovani-umrti?page={$page}");
    }
    
    public function ockovaniPozitivni(int $page, array $params = []): array
    {
        return $this->get("ockovani-pozitivni?page={$page}");
    }
    
    public function ockovaniHospitalizace(int $page, array $params = []): array
    {
        return $this->get("ockovani-hospitalizace?page={$page}");
    }
    
    public function umrti(int $page, array $params = []): array
    {
        $query = http_build_query($params);
        return $this->get("umrti?page={$page}&{$query}");
    }
    
    public function testyPcrAntigenni(int $page, array $params = []) : array
    {
        return $this->get("testy-pcr-antigenni?page={$page}");
    }
    
    protected function get(string $url): array
    {
        $date = date('Y-m-d-H');
        $path = self::CACHE_PATH . '/' . $date . '/';
        if (!file_exists($path)) {
            FileSystem::createDir($path);
        }
        
        $fileName = Strings::webalize($url) . '.json';
        $filePath = $path . $fileName;
        
        if (!file_exists($filePath)) {
            $response = $this->client->get($url . '&apiToken=' . $_ENV['MZCR_API_TOKEN']);
            $responseText = $response->getBody()->getContents();
            file_put_contents('nette.safe://' . $filePath, $responseText);
        }
        
        $json = FileSystem::read($filePath);
        return json_decode($json, true);
    }
}