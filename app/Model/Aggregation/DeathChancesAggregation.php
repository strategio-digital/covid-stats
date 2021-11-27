<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Aggregation;

use App\Model\Dataset\DeathsDataset;
use App\Model\Dataset\HospitalizedDataset;

class DeathChancesAggregation
{
    public function getStats(HospitalizedDataset $hospitalizedDataset, DeathsDataset $deathsDataset): array
    {
        $h = $hospitalizedDataset->getStats();
        $d = $deathsDataset->getStats();
        
        $sumHospitalize = $h['all']['abs'];
        
        $notVaxPercent = (($d['notVax']['abs'] + $d['firstVax']['abs']) / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        $secondVaxPercent = ($d['secondVax']['abs'] / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        $thirdVaxPercent = ($d['thirdVax']['abs'] / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        
        $notVaxRepetition = $notVaxPercent == 0 ? 0 : 100 / $notVaxPercent;
        $secondVaxRepetition = $secondVaxPercent == 0 ? 0 : 100 / $secondVaxPercent;
        $thirdVaxRepetition = $thirdVaxPercent == 0 ? 0 : 100 / $thirdVaxPercent;
    
        $avgPercent = (($d['all']['abs']) / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        //$avgPercent = ($notVaxPercent + $secondVaxPercent + $thirdVaxPercent) / 3;
        $avgRepetition = $avgPercent == 0 ? 0 : 100 / $avgPercent;
        
        return [
            'all' => [
                'percent' => $avgPercent,
                'repetitionBy100' => $avgRepetition
            ],
            'notVax' => [
                'percent' => $notVaxPercent,
                'repetitionBy100' => $notVaxRepetition
            ],
            'secondVax' => [
                'percent' => $secondVaxPercent,
                'repetitionBy100' => $secondVaxRepetition,
            ],
            'thirdVax' => [
                'percent' => $thirdVaxPercent,
                'repetitionBy100' => $thirdVaxRepetition
            ]
        ];
    }
}