<?php

namespace App\Enum;

class AnneeEnum
{
    public function numeroToAnnee(): array
    {
        return [
            0 => '2015 - 2016',
            1 => '2016 - 2017',
            2 => '2017 - 2018',
            3 => '2018 - 2019',
            4 => '2019 - 2020 - Hiver',
            5 => '2019 - 2020 - Eté',
        ];
    }
}
