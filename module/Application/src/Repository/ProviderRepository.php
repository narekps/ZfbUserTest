<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

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
}
