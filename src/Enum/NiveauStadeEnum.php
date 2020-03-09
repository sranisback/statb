<?php


namespace App\Enum;

class NiveauStadeEnum
{
    /**
     * @return array<int, string>
     */
    public function numeroVersNiveauDeStade()
    {
        return [
            0 => 'Prairie Verte',
            1 => 'Terrain aménagé',
            2 => 'Terrain bien aménagé',
            3 => 'Stade Correct',
            4 => 'Stade Ultra moderne',
            5 => 'Résidence'
        ];
    }
}
