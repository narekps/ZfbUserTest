<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Client as ClientEntity;

/**
 * Class ClientRepository
 *
 * @package Application\Repository
 */
class ClientRepository extends EntityRepository
{
    /**
     * @param string $inn
     * @param string $kpp
     *
     * @return \Application\Entity\Client|null
     */
    public function getByInnKpp(string $inn, string $kpp): ?ClientEntity
    {
        /** @var ClientEntity $client */
        $client = $this->findOneBy([
            'inn' => $inn,
            'kpp' => $kpp,
        ]);

        return $client;
    }

    /**
     * @param string $search
     *
     * @return \Application\Entity\Client[]
     */
    public function getList(string $search)
    {
        $qb = $this->createQueryBuilder('c')->select('c');
        if (!empty($search)) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('c.fullName', "'%{$search}%'"),
                $qb->expr()->like('c.inn', "'%{$search}%'")
            ));
        }

        /** @var ClientEntity[] $clients */
        $providers = $qb->getQuery()->getResult();

        return $providers;
    }
}
