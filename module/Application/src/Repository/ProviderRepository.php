<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Provider as ProviderEntity;

/**
 * Class ProviderRepository
 *
 * @package Application\Repository
 */
class ProviderRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getForSelect()
    {
        $qb = $this->createQueryBuilder('p')->select('p.id as id', 'p.fullName as value');
        $res = $qb->getQuery()->getArrayResult();

        $providers = [];

        foreach ($res as $row) {
            $providers[$row['id']] = $row['value'];
        }

        return $providers;
    }

    /**
     * @param string $search
     *
     * @return \Application\Entity\Provider[]
     */
    public function getList(string $search)
    {
        $qb = $this->createQueryBuilder('p')->select('p');
        if (!empty($search)) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('p.fullName', "'%{$search}%'"),
                $qb->expr()->like('p.inn', "'%{$search}%'")
            ));
        }

        /** @var ProviderEntity[] $providers */
        $providers = $qb->getQuery()->getResult();

        return $providers;
    }
}
