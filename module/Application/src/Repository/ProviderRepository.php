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
        $qb = $this->createQueryBuilder('p')->select('p, c');
        $qb->leftJoin('p.config', 'c');
        $qb->andWhere('c.identifier = :identifier')->setParameter('identifier', $identifier);

        /** @var ProviderEntity|null $provider */
        $provider = null;

        try {
            $provider = $qb->getQuery()->getSingleResult();
        } catch (DriverException $ex) {
        } catch (\Exception $ex) {
        }

        return $provider;
    }

    /**
     * @param int $id
     *
     * @return \Application\Entity\Provider|null
     */
    public function getById(int $id): ?ProviderEntity
    {
        $qb = $this->createQueryBuilder('p')->select('p, c');
        $qb->leftJoin('p.config', 'c');
        $qb->andWhere('p.id = :id')->setParameter('id', $id);

        try {
            /** @var ProviderEntity|null $provider */
            $provider = $qb->getQuery()->getSingleResult();
        } catch (\Exception $ex) {
        }

        return $provider;
    }
}
