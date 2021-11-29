<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Entity\Group;
use App\Model\MzcrApi;

class SummaryDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
        parent::__construct();
    }
    
    public function fetch(): SummaryDataset
    {
        // Todo
        // 0-17, 18-24, 25-29, 30-34, 35-39, 40-44, 45-49, 50-54, 55-59, 60-64, 65-69, 70-74, 75-79, 80+, nezařazeno
        //$response = $this->mzcrApi->ockovaniZakladniPrehled(2045);
        $this->data = [
            'nezarazeno_vac_1' => new Group('věk nezařazen | 1. dávka', 0, 0),
            'nezarazeno_vac_2' => new Group('věk nezařazen | 2. dávka', 0, 0),
            'nezarazeno_vac_3' => new Group('věk nezařazen | 3. dávka', 0, 0),
            'nezarazeno_vac_0' => new Group('věk nezařazen | neočkovaní', 0, 0),
            
            '0_5_vac_1' => new Group('0 - 5 let | 1. dávka', 0, 0),
            '0_5_vac_2' => new Group('0 - 5 let | 2. dávka', 0, 0),
            '0_5_vac_3' => new Group('0 - 5 let | 3. dávka', 0, 0),
            '0_5_vac_0' => new Group('0 - 5 let | neočkovaní', 0, 0),
        ];
        
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
}