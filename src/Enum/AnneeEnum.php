<?php

namespace App\Enum;

class AnneeEnum
{
    const TEST = 0;

    public function numeroToAnnee()
    {
        return [
            0 => '2015 - 2016',
            1 => '2016 - 2017',
            2 => '2017 - 2018',
            3 => '2018 - 2019',
            4 => '2019 - 2020',
        ];
    }
}
