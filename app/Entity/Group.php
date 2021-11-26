<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Entity;

class Group
{
    public function __construct(
        protected string $ageRange,
        protected int    $peopleInCR,
        protected int    $deaths,
    )
    {
    }
    
    /**
     * @return string
     */
    public function getAgeRange(): string
    {
        return $this->ageRange;
    }
    
    /**
     * @return int
     */
    public function getPeopleInCR(): int
    {
        return $this->peopleInCR;
    }
    
    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return $this->deaths;
    }
    
    /**
     * @return float
     */
    public function getDeathPercent(): float
    {
        if ($this->peopleInCR === 0) {
            return 0;
        }
        
        return (100 / $this->peopleInCR) * $this->deaths;
    }
    
    /**
     * @return float
     */
    public function getDeathIteration(): float
    {
        $percentage = $this->getDeathPercent();
        
        if ($percentage === 0.0) {
            return 0;
        }
        
        return 100 / $this->getDeathPercent();
    }
}