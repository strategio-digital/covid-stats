<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Model\MzcrApi;

class PositivesDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
        parent::__construct();
    }
    
    public function fetch(int $days): PositivesDataset
    {
        $expiration = (new \DateTime())->modify('+ 1hour');
        
        $response = $this->mzcrApi->ockovaniPozitivni($expiration, 1);
        $this->data = $this->fetchAll($response, 'ockovaniPozitivni', $expiration, 5, $days);
        
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getStats(): array
    {
        $sumDeaths = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['pozitivni_celkem'];
        }, 0);
    
        $sumNotVax = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['pozitivni_bez_ockovani'];
        }, 0);
    
        $sumFirstVax = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['pozitivni_nedokoncene_ockovani'];
        }, 0);
    
        $sumSecondVax = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['pozitivni_dokoncene_ockovani'];
        }, 0);
    
        $sumThirdVax = array_reduce($this->data, function (int $prev, array $death) {
            return $prev + $death['pozitivni_posilujici_davka'];
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