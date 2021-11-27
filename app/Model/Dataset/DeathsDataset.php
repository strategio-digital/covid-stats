<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Model\MzcrApi;

class DeathsDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
    }
    
    public function fetch(int $days): DeathsDataset
    {
        $response = $this->mzcrApi->ockovaniUmrti(1);
        $this->data = $this->fetchAll($response, 'ockovaniUmrti', $days);
        
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function countDays() : int
    {
        return count($this->data) - 1;
    }
    
    public function getStats(): array
    {
        $sumDeaths = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['zemreli_celkem'];
        }, 0);
        
        $sumNotVaccinated = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['zemreli_bez_ockovani'];
        }, 0);
        
        $sumFirstVaccination = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['zemreli_nedokoncene_ockovani'];
        }, 0);
        
        $sumSecondVaccination = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['zemreli_dokoncene_ockovani'];
        }, 0);
        
        $sumThirdVaccination = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['zemreli_posilujici_davka'];
        }, 0);
        
        return [
            'all' => [
                'percent' => 100,
                'absolute' => $sumDeaths,
            ],
            'notVaccinated' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumNotVaccinated,
                'absolute' => $sumNotVaccinated
            ],
            'firstVaccination' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumFirstVaccination,
                'absolute' => $sumFirstVaccination
            ],
            'secondVaccination' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumSecondVaccination,
                'absolute' => $sumSecondVaccination
            ],
            'thirdVaccination' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumThirdVaccination,
                'absolute' => $sumThirdVaccination
            ]
        ];
    }
}