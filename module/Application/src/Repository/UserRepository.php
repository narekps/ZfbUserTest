<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use ZfbUser\Repository\UserRepository as ZfbUserRepository;
use Application\Entity\User as UserEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Tracker as TrackerEntity;

/**
 * Class UserRepository
 *
 * @package Application\Repository
 */
class UserRepository extends ZfbUserRepository
{
    /**
     * @param \Application\Entity\Provider $provider
     *
     * @return \Application\Entity\User[]
     */
    public function getProviderUsers(ProviderEntity $provider)
    {
        $qb = $this->createQueryBuilder('u')->select('u');
        $qb->andWhere('u.provider = :provider')->setParameter('provider', $provider);

        /** @var UserEntity[] $users */
        $users = $qb->getQuery()->getResult();

        return $users;
    }

    /**
     * @param \Application\Entity\Tracker $tracker
     *
     * @return \Application\Entity\User[]
     */
    public function getTrackerUsers(TrackerEntity $tracker)
    {
        $qb = $this->createQueryBuilder('u')->select('u');
        $qb->andWhere('u.tracker = :tracker')->setParameter('tracker', $tracker);

        /** @var UserEntity[] $users */
        $users = $qb->getQuery()->getResult();

        return $users;
    }
}
