<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Model\MzcrApi;

class TestDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
    }
    
    public function fetch(int $days): TestDataset
    {
        $response = $this->mzcrApi->testyPcrAntigenni(1);
        $this->data = $this->fetchAll($response, 'testyPcrAntigenni', $days);
        
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getStats() : array
    {
        $sumAll = array_reduce($this->data, function (int $prev, array $test) {
            return $prev + $test['pocet_PCR_testy'] + $test['pocet_AG_testy'];
        }, 0);
        
        return ['sumAll' => $sumAll];
    }
}