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
        parent::__construct();
    }
    
    public function fetch(int $days): TestDataset
    {
        $expiration = (new \DateTime())->modify('+ 1hour');
        
        $response = $this->mzcrApi->testyPcrAntigenni($expiration, 1);
        $this->data = $this->fetchAll($response, 'testyPcrAntigenni', $expiration, 5, $days);
        
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