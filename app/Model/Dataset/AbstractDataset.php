<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

abstract class AbstractDataset
{
    protected function fetchAll(array $response, string $methodName, int $maxResults, array $params = []): array
    {
        $pages = (int)ceil($response['hydra:totalItems'] / 100);
        $data = $response['hydra:member'];
        
        for ($i = 2; $i <= $pages; $i++) {
            $data = array_merge($data, $this->mzcrApi->$methodName($i, $params)['hydra:member']);
        }
    
        $end = count($data);
        $maxResults = $maxResults > $end ? $end : $maxResults;
        $start = $end - $maxResults;
        
        $result = [];
        for ($i = $start; $i < $end; $i++) {
            $result[] = $data[$i];
        }
        
        return $result;
    }
}