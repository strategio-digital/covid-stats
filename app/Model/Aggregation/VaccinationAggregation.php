<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Aggregation;

use App\Helper\PeopleAges;
use App\Model\Dataset\VaccinatedDataset;

class VaccinationAggregation
{
    protected PeopleAges $peopleAges;
    
    public function __construct()
    {
        $this->peopleAges = new PeopleAges();
    }
    
    public function getStats(VaccinatedDataset $vaccinatedDataset): array
    {
        $v = $vaccinatedDataset->getStats();
        $sumPopulation = $this->peopleAges->countAllPeople();
        $sumNonVax = $sumPopulation - $v['sumSecondVax'];
        
        return [
            'sumPopulation' => $sumPopulation,
            'nonVax' => [
                'abs' => $sumNonVax,
                'percent' => ($sumNonVax / $sumPopulation) * 100,
            ],
            'firstVax' => [
                'abs' => $v['sumFirstVax'],
                'percent' => ($v['sumFirstVax'] / $sumPopulation) * 100
            ],
            'secondVax' => [
                'abs' => $v['sumSecondVax'],
                'percent' => ($v['sumSecondVax'] / $sumPopulation) * 100
            ],
            'thirdVax' => [
                'abs' => $v['sumThirdVax'],
                'percent' => ($v['sumThirdVax'] / $sumPopulation) * 100
            ]
        ];
    }
}