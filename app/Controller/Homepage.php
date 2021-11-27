<?php

namespace App\Controller;

use App\Model\Aggregation\DeathChancesAggregation;
use App\Model\Dataset\DeathsDataset;
use App\Model\Dataset\HospitalizedDataset;
use App\Model\Dataset\SummaryDataset;
use App\Model\MzcrApi;
use Latte\Engine;
use Nette\Http\Request;

class Homepage
{
    protected MzcrApi $mzcrApi;
    
    protected DeathsDataset $deathsDataset;
    
    protected HospitalizedDataset $hospitalizedDataset;
    
    protected SummaryDataset $summaryDataset;
    
    protected DeathChancesAggregation $deathChancesAggregation;
    
    public function __construct(protected Request $request, protected Engine $latte)
    {
        $this->mzcrApi = new MzcrApi();
        
        $this->deathsDataset = new DeathsDataset($this->mzcrApi);
        $this->hospitalizedDataset = new HospitalizedDataset($this->mzcrApi);
        $this->summaryDataset = new SummaryDataset($this->mzcrApi);
        
        $this->deathChancesAggregation = new DeathChancesAggregation();
    }
    
    public function index(): void
    {
        $days = (int) $this->request->getQuery('days') ?: 365.25 * 4;
        
        // Datasets
        $deaths = $this->deathsDataset->fetch($days);
        $hospitalized = $this->hospitalizedDataset->fetch($days);
        $summary = $this->summaryDataset->fetch();
        
        // Aggregations
        $deathChances = $this->deathChancesAggregation->getStats($hospitalized, $deaths);
    
        // Dates
        $endDate = new \DateTime();
        $startDate = (new \DateTime('now'))->modify("-{$deaths->countDays()} days");
        
        // Render
        $this->latte->render(__DIR__ . '/../../resource/Homepage/index.latte', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'deaths' => [
                'data' => $deaths->getData(),
                'stats' => $deaths->getStats()
            ],
            'hospitalized' => [
                'data' => $hospitalized->getData(),
                'stats' => $hospitalized->getStats(),
            ],
            'summary' => [
                'data' => $summary->getData(),
            ],
            
            'deathChances' => $deathChances,
        ]);
    }
}