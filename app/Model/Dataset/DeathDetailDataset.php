<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Model\MzcrApi;

class DeathDetailDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
    }
    
    public function fetch(string $date): DeathDetailDataset
    {
        $params = [
            'datum[before]' => $date,
            'datum[after]' => $date,
        ];
        
        $response = $this->mzcrApi->umrti(1, $params);
        $this->data = $this->fetchAll($response, 'umrti', 10000, $params);
        
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}