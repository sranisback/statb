<?php

namespace App\Factory;

use App\Entity\Defis;
use App\Entity\Teams;
use Nette\Utils\DateTime;

class DefiFactory
{
    /**
     * @param Teams $equipeDefiee
     * @param Teams $equipeOrigine
     * @return Defis
     */
    public function lancerDefis(Teams $equipeDefiee, Teams $equipeOrigine): \App\Entity\Defis
    {
        $dateDefis = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $defis = new Defis();

        if (!empty($dateDefis)) {
            $defis->setDateDefi($dateDefis);
            $defis->setEquipeDefiee($equipeDefiee);
            $defis->setEquipeOrigine($equipeOrigine);
        }

        return $defis;
    }
}
