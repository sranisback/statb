<?php


namespace App\Service;

use App\Entity\Coaches;
use App\Entity\HistoriqueBlessure;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Sponsors;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param array $request
     * @param class-string $entity
     * @param UserPasswordEncoderInterface|null $encoder
     */
    public function traiteModification(
        array $request,
        string $entity,
        UserPasswordEncoderInterface $encoder = null
    ) : void {
        /** @var class-string $entity */
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

        /** @var Coaches|Matches|Players|Teams|HistoriqueBlessure|Sponsors $object */
        $object = $this->doctrineEntityManager
            ->getRepository($entity)
            ->findOneBy([$id => $request['pk']]);

        switch ($request['name']) {
            case 'Password':
                $object->setPassword($encoder->encodePassword($object, $object->getPassword()));
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
