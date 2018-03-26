<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Provider as ProviderEntity;
use Doctrine\DBAL\Exception\DriverException;

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

    /**
     * @param string $identifier
     *
     * @return \Application\Entity\Provider|null
     */
    public function getByIdentifier(string $identifier): ?ProviderEntity
    {
        /** @var ProviderEntity|null $provider */
        $provider = null;

        try {
            $provider = $this->findOneBy(['identifier' => $identifier]);
        } catch (DriverException $ex) {
        }

        return $provider;
    }
}
