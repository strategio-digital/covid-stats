<?php
/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Helper;

class PeopleAges
{
    protected array $data = [
        112051,
        114505,
        115264,
        114188,
        112815,
        112333,
        109624,
        110642,
        110382,
        120414,
        122095,
        123576,
        119162,
        109443,
        103708,
        99048,
        95329,
        94886,
        94371,
        94396,
        93014,
        94521,
        95609,
        96795,
        103093,
        114277,
        128549,
        130178,
        138430,
        140458,
        139504,
        144786,
        143483,
        145646,
        148708,
        148890,
        148955,
        151889,
        152513,
        160751,
        176750,
        182008,
        184246,
        188738,
        191704,
        193117,
        180700,
        163690,
        154043,
        147728,
        141653,
        134535,
        134330,
        135489,
        140229,
        145997,
        140360,
        125875,
        122148,
        119121,
        117089,
        125658,
        133711,
        137407,
        137702,
        136954,
        136516,
        137281,
        136216,
        131960,
        126426,
        128130,
        130059,
        122445,
        95914,
        97784,
        91879,
        78664,
        72388,
        67914,
        57117,
        51605,
        45568,
        41773,
        38432,
        35257,
        31721,
        29139,
        25059,
        21413,
        16541,
        13289,
        10111,
        7583,
        5457,
        3922,
        2875,
        1796,
        1131,
        598,
        713
    ];
    
    public function countPeople(int $age): int
    {
        return $this->data[$age];
    }
    
    public function countPeopleByGroup(array $ages) : int
    {
        $count = 0;
        
        foreach ($ages as $age) {
            $count += $this->countPeople($age);
        }
        
        return $count;
    }
    
    public function countAllPeople() : int
    {
        return array_sum($this->data);
    }
}