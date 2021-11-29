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
        parent::__construct();
    }
    
    public function fetch(string $date): DeathDetailDataset
    {
        $params = [
            'datum[before]' => $date,
            'datum[after]' => $date,
        ];
    
        $expiration = (new \DateTime())->modify('+ 1hour');
        
        $response = $this->mzcrApi->umrti($expiration, 1, $params);
        $this->data = $this->fetchAll($response, 'umrti',  $expiration, 5, $response['hydra:totalItems'], $params);
        
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}