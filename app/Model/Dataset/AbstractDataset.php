<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

abstract class AbstractDataset
{
    protected FileStorage $storage;
    
    protected function __construct()
    {
        $path = __DIR__ . '/../../../temp/mzcr-api';
        
        if (!file_exists($path)) {
            FileSystem::createDir($path);
        }
        
        $this->storage = new FileStorage($path);
    }
    
    protected function fetchAll(array $response, string $methodName, \DateTime $expiration, int $maxRequests, int $maxResults, array $params = []): array
    {
        $pages = (int)ceil($response['hydra:totalItems'] / 100);
        $data = $response['hydra:member'];
        
        $kvsName = 'pagination_' . Strings::webalize($methodName . http_build_query($params));
        $requestStore = $this->storage->read($kvsName) ?: ['lastPage' => 0];
        $lastSaved = $requestStore['lastPage'];
        
        for ($i = $lastSaved + 1; ($i <= $lastSaved + $maxRequests) && ($i <= $pages); $i++) {
            $requestStore['lastPage'] = $i;
            $this->storage->write($kvsName, $requestStore, [
                Cache::EXPIRE => $expiration->getTimestamp()
            ]);
        }
        
        for ($i = 2; $i <= $requestStore['lastPage']; $i++) {
            $data = array_merge($data, $this->mzcrApi->$methodName($expiration, $i, $params)['hydra:member']);
        }
        
        usort($data, function (array $a, array $b) {
            $date1 = new \DateTime($a['datum']);
            $date2 = new \DateTime($b['datum']);
            return $date1->getTimestamp() <=> $date2->getTimestamp();
        });
        
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