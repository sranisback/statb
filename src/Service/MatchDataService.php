<?php
/**
 * Created by PhpStorm.
 * User: Sran_isback
 * Date: 08/03/2019
 * Time: 11:35
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class MatchDataService
{

    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }
}