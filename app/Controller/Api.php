<?php

namespace App\Controller;

use App\Model\Dataset\DeathDetailDataset;
use App\Model\MzcrApi;
use Latte\Engine;
use Nette\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Api
{
    protected MzcrApi $mzcrApi;
    
    protected DeathDetailDataset $deathDetailDataset;
    
    public function __construct(protected Request $request, protected Engine $latte)
    {
        $this->mzcrApi = new MzcrApi();
        $this->deathDetailDataset = new DeathDetailDataset($this->mzcrApi);
    }
    
    public function index(): void
    {
        $date = $this->request->getQuery('date');
        
        $dataset = $this->deathDetailDataset->fetch($date);
        $data = $dataset->getData();
    
        usort($data, function (array $a, array $b) {
            return $a['vek'] <=> $b['vek'];
        });
        
        $html = $this->latte->renderToString(__DIR__ . '/../../resource/Api/death-detail-data.latte', ['data' => $data]);
        
        $response = new JsonResponse($html);
        $response->send();
    }
}