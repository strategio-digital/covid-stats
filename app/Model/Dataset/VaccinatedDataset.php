<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Model\Dataset;

use App\Model\MzcrApi;
use Nette\Caching\Cache;

class VaccinatedDataset extends AbstractDataset
{
    protected array $data = [];
    
    public function __construct(protected MzcrApi $mzcrApi)
    {
        parent::__construct();
    }
    
    public function fetch(): VaccinatedDataset
    {
        $expiration = (new \DateTime())->modify('+ 1 hour');
        $expirationAll = (new \DateTime())->modify('+ 10 years');
        $response = $this->mzcrApi->ockovaniDemografie($expiration, 1);
        
        $pages = (int)ceil($response['hydra:totalItems'] / 100);
        $pagination = $this->storage->read('pagination_ockovanidemografie') ?: ['lastPage' => 0];
        
        if ($pagination['lastPage'] === $pages) {
            $cache = new Cache($this->storage, 'vaccinated-dataset');
            $this->data = $cache->load('fetchAll', function (array|null &$dependencies) use ($response, $expirationAll) {
                $dependencies[Cache::EXPIRE] = (new \DateTime())->modify('+ 4 hours');
                return $this->fetchAll($response, 'ockovaniDemografie', $expirationAll, 100, $response['hydra:totalItems']);
            });
        } else {
            $this->data = $this->fetchAll($response, 'ockovaniDemografie', $expirationAll, 100, $response['hydra:totalItems']);
        }
        
        return $this;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getStats(): array
    {
        $groupedByDate = [];
        foreach ($this->data as $data) {
            @$groupedByDate[$data['datum']][$data['poradi_davky']] += $data['pocet_davek'];
        }
        
        $groupedByDate = array_values($groupedByDate);
        
        $sumByVax = [
            1 => 0,
            2 => 0,
            3 => 0
        ];
        
        foreach ($groupedByDate as $data) {
            $sumByVax[1] += @$data[1];
            $sumByVax[2] += @$data[2];
            $sumByVax[3] += @$data[3];
        }
        
        $sumAll = $sumByVax[1] + $sumByVax[2] + $sumByVax[3];
        unset($groupedByDate);
        
        
        return [
            'sumAll' => $sumAll,
            'sumFirstVax' => $sumByVax[1],
            'sumSecondVax' => $sumByVax[2],
            'sumThirdVax' => $sumByVax[3],
        ];
    }
}