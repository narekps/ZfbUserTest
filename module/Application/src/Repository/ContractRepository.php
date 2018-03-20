<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Contract as ContractEntity;
use Application\Entity\Provider as ProviderEntity;

/**
 * Class ContractRepository
 *
 * @package Application\Repository
 */
class ContractRepository extends EntityRepository
{
    /**
     * @param \Application\Entity\Provider $provider
     *
     * @return array
     */
    public function getForSelect(ProviderEntity $provider): array
    {
        $contracts = $this->getByProvider($provider);
        $res = [];
        foreach ($contracts as $contract) {
            $res[$contract->getId()] = $contract->getDisplayName();
        }

        return $res;
    }

    /**
     * @param int $id
     *
     * @return \Application\Entity\Contract
     */
    public function getById(int $id)
    {
        /** @var ContractEntity $contract */
        $contract = $this->findOneBy(['id' => $id]);

        return $contract;
    }

    /**
     * @param \Application\Entity\Provider $provider
     *
     * @return \Application\Entity\Contract[]
     */
    public function getByProvider(ProviderEntity $provider): array
    {
        $qb = $this->createQueryBuilder('c')->select('c');
        $qb->andWhere('c.provider = :provider')->setParameter('provider', $provider);
        $qb->orderBy('c.etpContractDate', 'DESC');

        /** @var ContractEntity[] $contracts */
        $contracts = $qb->getQuery()->getResult();

        return $contracts;
    }
}
