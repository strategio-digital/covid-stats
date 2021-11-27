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
        
        $notVax = (($d['notVax']['abs'] + $d['firstVax']['abs']) / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        $secondVax = ($d['secondVax']['abs'] / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        $thirdVax = ($d['thirdVax']['abs'] / ($sumHospitalize == 0 ? 1 : $sumHospitalize)) * 100;
        
        
        return [
            'all' => [
                'percent' => 0,
                'repetitionBy100' => 0
            ],
            'notVax' => [
                'percent' => $notVax,
                'repetitionBy100' => $notVax == 0 ? 0 : 100 / $notVax
            ],
            'secondVax' => [
                'percent' => $secondVax,
                'repetitionBy100' => $secondVax == 0 ? 0 : 100 / $secondVax,
            ],
            'thirdVax' => [
                'percent' => $thirdVax,
                'repetitionBy100' => $thirdVax == 0 ? 0 : 100 / $thirdVax
            ]
        ];
    }
}