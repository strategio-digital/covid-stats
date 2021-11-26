<?php

namespace App\Model;

use GuzzleHttp\Client;

class MzcrApi
{
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
    
    public function hospitalizace(int $page): array
    {
        $response = $this->client->get("hospitalizace?page={$page}&apiToken=" . $_ENV['MZCR_API_TOKEN']);
        return json_decode($response->getBody()->getContents(), true);
    }
    
    public function ockovani(int $page): array
    {
        $response = $this->client->get("ockovani?page={$page}&apiToken=" . $_ENV['MZCR_API_TOKEN']);
        return json_decode($response->getBody()->getContents(), true);
    }
    
    public function ockovaniZakladniPrehled(int $page): array
    {
        $response = $this->client->get("ockovani-zakladni-prehled?page={$page}&apiToken=" . $_ENV['MZCR_API_TOKEN']);
        return json_decode($response->getBody()->getContents(), true);
    }
    
    public function ockovaniUmrti(int $page): array
    {
        $response = $this->client->get("ockovani-umrti?page={$page}&apiToken=" . $_ENV['MZCR_API_TOKEN']);
        return json_decode($response->getBody()->getContents(), true);
    }
}