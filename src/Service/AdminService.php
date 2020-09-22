<?php


namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class AdminService
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private \Doctrine\ORM\EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function traiteModification($request, $entity, $encoder = null)
    {
        switch ($entity) {
            case Coaches::class:
                $id = 'coachId';
            break;
            case Matches::class:
                $id = 'matchId';
            break;
            case Players::class:
                $id = 'playerId';
            break;
            case Teams::class:
                $id = 'teamId';
                break;
            default:
                $id = 'id';
            break;
        }

        $object = $this->doctrineEntityManager
            ->getRepository($entity)
            ->findOneBy([$id => $request['pk']]);

        switch ($request['name']) {
            case 'Passwd':
                $object->setPasswd($encoder->encodePassword($object, $object->getPasswd()));
                break;

            case 'Roles':
                $object->setRoles(['role' => 'ROLE_' . $request['value']]);
                break;

            default:
                $fieldName ='set' . $request['name'];
                $object->$fieldName($request['value']);
                break;
        }

        $this->doctrineEntityManager->flush();
    }
}
