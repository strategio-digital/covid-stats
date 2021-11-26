<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

class MzcrApi
{
    const CACHE_PATH = __DIR__ . '/../../temp/mzcr-api/';
    
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
    
    public function ockovaniZakladniPrehled(int $page): array
    {
        return $this->get("ockovani-zakladni-prehled?page={$page}");
    }
    
    public function ockovani(int $page): array
    {
        return $this->get("ockovani?page={$page}");
    }
    
    public function ockovaniUmrti(int $page): array
    {
        return $this->get("ockovani-umrti?page={$page}");
    }
    
    public function ockovaniPozitivni(int $page): array
    {
        return $this->get("ockovani-pozitivni?page={$page}");
    }
    
    public function ockovaniHospitalizace(int $page): array
    {
        return $this->get("ockovani-hospitalizace?page={$page}");
    }
    
    protected function get(string $url) : array
    {
        if (!file_exists(self::CACHE_PATH)) {
            FileSystem::createDir(self::CACHE_PATH);
        }
    
        $fileName = Strings::webalize($url) . '.json';
        if (Strings::endsWith($fileName, 'page-1.json')) {
            $fileName = date('jnyH') . '-' . $fileName;
        }
        
        $filePath = self::CACHE_PATH . $fileName;
        
        if (!file_exists($filePath)) {
            $response = $this->client->get($url . '&apiToken=' . $_ENV['MZCR_API_TOKEN']);
            $responseText = $response->getBody()->getContents();
            file_put_contents('nette.safe://' . $filePath, $responseText);
        }
        
        $json = FileSystem::read($filePath);
        return json_decode($json, true);
    }
}