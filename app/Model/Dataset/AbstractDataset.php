<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

abstract class AbstractDataset
{
    protected function fetchAll(array $response, string $methodName): array
    {
        $pages = (int)ceil($response['hydra:totalItems'] / 100);
        $data = $response['hydra:member'];
        
        for ($i = 2; $i <= $pages; $i++) {
            $data = array_merge($data, $this->mzcrApi->$methodName($i)['hydra:member']);
        }
        
        return $data;
    }
}