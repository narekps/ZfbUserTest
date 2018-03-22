<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Tariff as TariffEntity;
use Application\Entity\Provider as ProviderEntity;

/**
 * Class TariffRepository
 *
 * @package Application\Repository
 */
class TariffRepository extends EntityRepository
{

    /**
     * @param \Application\Entity\Provider $provider
     * @param string                       $status Статус тарифа, пустая строка - все статусы
     *
     * @return \Application\Entity\Tariff[]
     */
    public function getList(ProviderEntity $provider, string $status): array
    {
        $qb = $this->createQueryBuilder('t')->select('t');
        $qb->andWhere('t.provider = :provider')->setParameter('provider', $provider);
        if (!empty($status)) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status);
        }

        /** @var TariffEntity[] $tariffs */
        $tariffs = $qb->getQuery()->getResult();

        return $tariffs;
    }

    /**
     * @param int $id
     *
     * @return \Application\Entity\Tariff|null
     */
    public function getById(int $id): ?TariffEntity
    {
        /** @var TariffEntity $tariff */
        $tariff = $this->findOneBy(['id' => $id]);

        return $tariff;
    }
}
