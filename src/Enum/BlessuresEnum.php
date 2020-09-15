<?php


namespace App\Enum;

class BlessuresEnum
{
    /**
     * @return string[]
     */
    public function numeroToBlessure(): array
    {
        return [
            30 => 'Commotion',
            40 => 'Blessure Grave non notée',
            50 => 'Perte de Stat non notée',
            41 => 'Mâchoire Fracassée',
            42 => 'Côtes cassées',
            43 => 'Bras fracturé',
            44 => 'Jambe cassée',
            45 => 'Main écrasée',
            46 => 'Œil crevé',
            47 => 'Ligaments arrachés',
            48 => 'Nerf Coincé',
            51 => 'Dos abîmé',
            52 => 'Genou déboîté',
            53 => 'Cheville détruite',
            54 => 'Hanche démolie',
            55 => 'Fracture du crâne',
            56 => 'Commotion grave',
            57 => 'Cou brisé',
            58 => 'Clavicule défoncée',
            60 => 'Mort'
        ];
    }
}
