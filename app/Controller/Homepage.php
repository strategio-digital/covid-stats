<?php

namespace App\Controller;

use App\Model\Aggregation\DeathPrediction;
use App\Model\Aggregation\TestAggregation;
use App\Model\Dataset\DeathsDataset;
use App\Model\Dataset\HospitalizedDataset;
use App\Model\Dataset\PositivesDataset;
use App\Model\Dataset\SummaryDataset;
use App\Model\Dataset\TestDataset;
use App\Model\MzcrApi;
use Latte\Engine;
use Nette\Http\Request;

class Homepage
{
    protected MzcrApi $mzcrApi;
    
    protected DeathsDataset $deathsDataset;
    
    protected HospitalizedDataset $hospitalizedDataset;
    
    protected PositivesDataset $positivesDataset;
    
    protected TestDataset $testDataset;
    
    protected SummaryDataset $summaryDataset;
    
    protected DeathPrediction $deathPrediction;
    
    protected TestAggregation $testAggregation;
    
    public function __construct(protected Request $request, protected Engine $latte)
    {
        $this->mzcrApi = new MzcrApi();
        
        $this->deathsDataset = new DeathsDataset($this->mzcrApi);
        $this->hospitalizedDataset = new HospitalizedDataset($this->mzcrApi);
        $this->positivesDataset = new PositivesDataset($this->mzcrApi);
        $this->testDataset = new TestDataset($this->mzcrApi);
        $this->summaryDataset = new SummaryDataset($this->mzcrApi);
        
        $this->deathPrediction = new DeathPrediction();
        $this->testAggregation = new TestAggregation();
    }
    
    public function index(): void
    {
        $firstDate = new \DateTime('2021-01-01');
        $lastDate = new \DateTime();
        $maxDays = $firstDate->diff($lastDate)->days + 1;
        
        // Get days from request
        $requestDays = ((int)$this->request->getQuery('days') ?: $maxDays);
        $days = ($requestDays < $maxDays && $requestDays > 0 ? $requestDays : $maxDays);
    
        // Datasets
        $deaths = $this->deathsDataset->fetch($days);
        $hospitalized = $this->hospitalizedDataset->fetch($days);
        $positives = $this->positivesDataset->fetch($days);
        $tests = $this->testDataset->fetch($days - 1);
        $summary = $this->summaryDataset->fetch();
        
        // Aggregations
        $predictionForPositives = $this->deathPrediction->getStats($deaths, $positives->getStats());
        $predictionForHospitalized = $this->deathPrediction->getStats($deaths, $hospitalized->getStats());
        $testStats = $this->testAggregation->getStats($tests, $positives->getStats());
        
        // Render
        $this->latte->render(__DIR__ . '/../../resource/Homepage/index.latte', [
            'startDate' => (clone $lastDate)->modify('-' . $days - 1 . 'days'),
            'endDate' => $lastDate,
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
            'tests' => [
                'stats' => $testStats
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