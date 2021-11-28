<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Model\MzcrApi;

class HospitalizedDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
    }
    
    public function fetch(int $days): HospitalizedDataset
    {
        $response = $this->mzcrApi->ockovaniHospitalizace(1);
        $this->data = $this->fetchAll($response, 'ockovaniHospitalizace', $days);
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function countDays(): int
    {
        return count($this->data) - 1;
    }
    
    public function getStats(): array
    {
        $sumDeaths = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_celkem'];
        }, 0);
        
        $sumNotVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_bez_ockovani'];
        }, 0);
        
        $sumFirstVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_nedokoncene_ockovani'];
        }, 0);
        
        $sumSecondVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_dokoncene_ockovani'];
        }, 0);
        
        $sumThirdVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_posilujici_davka'];
        }, 0);
        
        return [
            'all' => [
                'percent' => 100,
                'abs' => $sumDeaths,
            ],
            'notVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumNotVax,
                'abs' => $sumNotVax
            ],
            'firstVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumFirstVax,
                'abs' => $sumFirstVax
            ],
            'secondVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumSecondVax,
                'abs' => $sumSecondVax
            ],
            'thirdVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumThirdVax,
                'abs' => $sumThirdVax
            ]
        ];
    }
}