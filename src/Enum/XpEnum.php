<?php


namespace App\Enum;

class XpEnum
{
    public static function tableauXpBb2016() : array
    {
        return [
            0 => 5,
            1 => 15,
            2 => 30,
            3 => 50,
            4 => 75
        ];
    }

    public static function tableauRecompenseXp() : array
    {
        return [
            'LAN' => 1,
            'REU' => 1,
            'DET' => 1,
            'INT' => 2,
            'CAS' => 2,
            'TD' => 3,
            'MVP2020' => 4,
            'MVP2016' => 5
        ];
    }

    public static function tableauXpBb2020() : array
    {
        return [
            'PH' => [
                0 => 3,
                1 => 4,
                2 => 6,
                3 => 8,
                4 => 10,
                5 => 51
            ],
            'PSH' => [
                0 => 6,
                1 => 8,
                2 => 12,
                3 => 16,
                4 => 20,
                5 => 30
            ],
            'S' => [
                0 => 12,
                1 => 14,
                2 => 18,
                3 => 22,
                4 => 26,
                5 => 40
            ],
            'C' => [
                0 => 18,
                1 => 20,
                2 => 24,
                3 => 28,
                4 => 32,
                5 => 50
            ]
        ];
    }

    public static function tableauXpParNiveau() : array
    {
        return [
            0 => [
                'PH' => 3,
                'PSH' => 6,
                'S' => 12,
                'C' => 18
            ],
            1 => [
                'PH' => 4,
                'PSH' => 8,
                'S' => 14,
                'C' => 20
            ],
            2 => [
                'PH' => 6,
                'PSH' => 12,
                'S' => 18,
                'C' => 24
            ],
            3 => [
                'PH' => 8,
                'PSH' => 16,
                'S' => 22,
                'C' => 28
            ],
            4 => [
                'PH' => 10,
                'PSH' => 20,
                'S' => 26,
                'C' => 32
            ],
            5 => [
                'PH' => 15,
                'PSH' => 30,
                'S' => 40,
                'C' => 50
            ]
        ];
    }
}
