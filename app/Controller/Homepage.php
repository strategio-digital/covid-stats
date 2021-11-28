<?php

namespace App\Controller;

use App\Model\Aggregation\DeathPrediction;
use App\Model\Dataset\DeathsDataset;
use App\Model\Dataset\HospitalizedDataset;
use App\Model\Dataset\PositivesDataset;
use App\Model\Dataset\SummaryDataset;
use App\Model\MzcrApi;
use Latte\Engine;
use Nette\Http\Request;

class Homepage
{
    protected MzcrApi $mzcrApi;
    
    protected DeathsDataset $deathsDataset;
    
    protected HospitalizedDataset $hospitalizedDataset;
    
    protected PositivesDataset $positivesDataset;
    
    protected SummaryDataset $summaryDataset;
    
    protected DeathPrediction $deathPrediction;
    
    public function __construct(protected Request $request, protected Engine $latte)
    {
        $this->mzcrApi = new MzcrApi();
        
        $this->deathsDataset = new DeathsDataset($this->mzcrApi);
        $this->hospitalizedDataset = new HospitalizedDataset($this->mzcrApi);
        $this->positivesDataset = new PositivesDataset($this->mzcrApi);
        $this->summaryDataset = new SummaryDataset($this->mzcrApi);
        
        $this->deathPrediction = new DeathPrediction();
    }
    
    public function index(): void
    {
        $days = (int)$this->request->getQuery('days') ?: 365.25 * 4;
        
        // Datasets
        $deaths = $this->deathsDataset->fetch($days);
        $hospitalized = $this->hospitalizedDataset->fetch($days);
        $positives = $this->positivesDataset->fetch($days);
        $summary = $this->summaryDataset->fetch();
        
        // Predictions
        $predictionForPositives = $this->deathPrediction->getStats($deaths, $positives->getStats());
        $predictionForHospitalized = $this->deathPrediction->getStats($deaths, $hospitalized->getStats());
        
        // Dates
        $endDate = $deaths->getData()[$deaths->countDays()]['datum'];
        $endDate = new \DateTime($endDate);
        $startDate = (clone $endDate)->modify("-{$deaths->countDays()} days");
        
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
                'stats' => $hospitalized->getStats()
            ],
            'positives' => [
                'data' => $positives->getData(),
                'stats' => $positives->getStats()
            ],
            'summary' => [
                'data' => $summary->getData()
            ],
            'deathPrediction' => [
                'forPositives' => $predictionForPositives,
                'forHospitalized' => $predictionForHospitalized
            ]
        ]);
    }
}