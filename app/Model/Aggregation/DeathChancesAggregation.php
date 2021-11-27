<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Aggregation;

class DeathChancesAggregation
{
    public function getStats(array $deaths, array $hospitalized): array
    {
        $all = 0;
        if ($hospitalized['all']['absolute'] !== 0) {
            $all = ($deaths['all']['absolute'] / $hospitalized['all']['absolute']) * 100;
        }
        
        $notVaccinated = 0;
        if ($hospitalized['notVaccinated']['absolute'] + $hospitalized['firstVaccination']['absolute'] !== 0) {
            $notVaccinated = (
                    ($deaths['notVaccinated']['absolute'] + $deaths['firstVaccination']['absolute']) /
                    ($hospitalized['notVaccinated']['absolute'] + $hospitalized['firstVaccination']['absolute'])
                ) * 100;
        }
        
        $secondVaccination = 0;
        if ($hospitalized['secondVaccination']['absolute'] !== 0) {
            $secondVaccination = ($deaths['secondVaccination']['absolute'] / $hospitalized['secondVaccination']['absolute']) * 100;
        }
        
        $thirdVaccination = 0;
        if ($hospitalized['thirdVaccination']['absolute'] !== 0) {
            $thirdVaccination = ($deaths['thirdVaccination']['absolute'] / $hospitalized['thirdVaccination']['absolute']) * 100;
        }
        
        return [
            'all' => [
                'percent' => $all,
                'repetitionBy100' => $all === 0 ? 0 : 100 / $all
            ],
            'notVaccinated' => [
                'percent' => $notVaccinated,
                'repetitionBy100' => $notVaccinated === 0 ? 0 : 100 / $notVaccinated
            ],
            'secondVaccination' => [
                'percent' => $secondVaccination,
                'repetitionBy100' => $secondVaccination === 0 ? 0 : 100 / $secondVaccination,
            ],
            'thirdVaccination' => [
                'percent' => $thirdVaccination,
                'repetitionBy100' => $thirdVaccination === 0 ? 0 : 100 / $thirdVaccination
            ]
        ];
    }
}