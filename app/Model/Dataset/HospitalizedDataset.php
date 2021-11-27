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
        
        $sumnotVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_bez_ockovani'];
        }, 0);
        
        $sumfirstVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_nedokoncene_ockovani'];
        }, 0);
        
        $sumsecondVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_dokoncene_ockovani'];
        }, 0);
        
        $sumthirdVax = array_reduce($this->data, function (int $prev, array $hospitalized) {
            return $prev + $hospitalized['hospitalizovani_posilujici_davka'];
        }, 0);
        
        return [
            'all' => [
                'percent' => 100,
                'abs' => $sumDeaths,
            ],
            'notVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumnotVax,
                'abs' => $sumnotVax
            ],
            'firstVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumfirstVax,
                'abs' => $sumfirstVax
            ],
            'secondVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumsecondVax,
                'abs' => $sumsecondVax
            ],
            'thirdVax' => [
                'percent' => $sumDeaths === 0 ? 0 : (100 / $sumDeaths) * $sumthirdVax,
                'abs' => $sumthirdVax
            ]
        ];
    }
}