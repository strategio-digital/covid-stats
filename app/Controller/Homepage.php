<?php

namespace App\Controller;

use App\Model\Dataset\DeathsDataset;
use App\Model\Dataset\PositivesDataset;
use App\Model\Dataset\SummaryDataset;
use App\Model\MzcrApi;
use Latte\Engine;

class Homepage
{
    protected Engine $latte;
    
    protected MzcrApi $mzcrApi;
    
    protected DeathsDataset $deathsDataset;
    
    protected PositivesDataset $positivesDataset;
    
    protected SummaryDataset $summaryDataset;
    
    public function __construct()
    {
        $this->mzcrApi = new MzcrApi();
    
        $this->deathsDataset = new DeathsDataset($this->mzcrApi);
        $this->positivesDataset = new PositivesDataset($this->mzcrApi);
        $this->summaryDataset = new SummaryDataset($this->mzcrApi);
    
        $this->latte = new Engine();
        $this->latte->setTempDirectory(__DIR__ . '/../../temp');
    }
    
    public function index(): void
    {
        // Datasets
        $deaths = $this->deathsDataset->fetch();
        $summary = $this->summaryDataset->fetch();
    
        // Dates
        $endDate = new \DateTime();
        $startDate = clone $endDate;
        $startDate->modify("-{$deaths->countDays()} days");
        
        // Render
        $this->latte->render(__DIR__ . '/../../resource/Homepage/index.latte', [
            'startDate' => $startDate->modify('-2 months'),
            'endDate' => $endDate,
            'deaths' => $deaths->getStats(),
            'summary' => $summary->getData(),
        ]);
    }
}