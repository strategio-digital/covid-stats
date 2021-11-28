<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Aggregation;

use App\Model\Dataset\TestDataset;

class TestAggregation
{
    public function getStats(TestDataset $testDataset, array $positives) : array
    {
        $sum = $testDataset->getStats()['sumAll'];
        $sumPositive = $positives['all']['abs'];
        $sumNegative = $sum - $sumPositive;
        
        return [
            'sumAll' => $sum,
            'positive' => [
                'abs' => $sumPositive,
                'percent' => ($sumPositive / ($sum != 0 ? $sum : 1)) * 100
            ],
            'negative' => [
                'abs' => $sumNegative,
                'percent' => ($sumNegative / ($sum != 0 ? $sum : 1)) * 100
            ]
        ];
    }
}