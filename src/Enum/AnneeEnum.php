<?php

namespace App\Enum;

class AnneeEnum
{
    /**
     * @return array<int, string>
     */
    public static function numeroToAnnee()
    {
        return [
            0 => '2015 - 2016',
            1 => '2016 - 2017',
            2 => '2017 - 2018',
            3 => '2018 - 2019',
            4 => '2019 - 2020 - Hiver',
            5 => '2019 - 2020 - Eté',
            6 => '2020 - 2021 - Hiver',
            7 => '2021 - 2022 - Hiver',
            8 => '2022 - 2023',
        ];
    }
}
