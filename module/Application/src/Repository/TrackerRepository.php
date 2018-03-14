<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Tracker as TrackerEntity;

/**
 * Class TrackerRepository
 *
 * @package Application\Repository
 */
class TrackerRepository extends EntityRepository
{
    /**
     * @param string $search
     *
     * @return \Application\Entity\Tracker[]
     */
    public function getList(string $search)
    {
        $qb = $this->createQueryBuilder('t')->select('t');
        if (!empty($search)) {
            $qb->andWhere($qb->expr()->like('t.fullName', "'%{$search}%'"));
        }

        /** @var TrackerEntity[] $trackers */
        $trackers = $qb->getQuery()->getResult();

        return $trackers;
    }
}
