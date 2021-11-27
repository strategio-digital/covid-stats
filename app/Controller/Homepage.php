<?php

namespace App\Controller;

use App\Model\Aggregation\DeathChancesAggregation;
use App\Model\Dataset\DeathsDataset;
use App\Model\Dataset\HospitalizedDataset;
use App\Model\Dataset\SummaryDataset;
use App\Model\MzcrApi;
use Latte\Engine;
use Symfony\Component\HttpFoundation\Request;

class Homepage
{
    protected Engine $latte;
    
    protected MzcrApi $mzcrApi;
    
    protected DeathsDataset $deathsDataset;
    
    protected HospitalizedDataset $hospitalizedDataset;
    
    protected SummaryDataset $summaryDataset;
    
    protected DeathChancesAggregation $deathChancesAggregation;
    
    protected int $days;
    
    public function __construct(protected Request $request)
    {
        $this->days = (int) $this->request->get('days', 365.25 * 4);
        
        $this->mzcrApi = new MzcrApi();
        
        $this->deathsDataset = new DeathsDataset($this->mzcrApi);
        $this->hospitalizedDataset = new HospitalizedDataset($this->mzcrApi);
        $this->summaryDataset = new SummaryDataset($this->mzcrApi);
        
        $this->deathChancesAggregation = new DeathChancesAggregation();
    
        $this->latte = new Engine();
        $this->latte->setTempDirectory(__DIR__ . '/../../temp/latte');
    }
    
    public function index(): void
    {
        // Datasets
        $deaths = $this->deathsDataset->fetch($this->days);
        $hospitalized = $this->hospitalizedDataset->fetch($this->days);
        $summary = $this->summaryDataset->fetch();
        
        // Aggregations
        $deathChances = $this->deathChancesAggregation->getStats($deaths->getStats(), $hospitalized->getStats());
    
        // Dates
        $endDate = new \DateTime();
        $startDate = clone $endDate;
        $startDate->modify("-{$deaths->countDays()} days");
        
        // Render
        $this->latte->render(__DIR__ . '/../../resource/Homepage/index.latte', [
            'startDate' => $startDate->modify('-2 months'),
            'endDate' => $endDate,
            'deaths' => [
                'stats' => $deaths->getStats()
            ],
            'hospitalized' => [
                'stats' => $hospitalized->getStats(),
            ],
            'summary' => [
                'data' => $summary->getData(),
            ],
            
            'deathChances' => $deathChances,
        ]);
    }
}