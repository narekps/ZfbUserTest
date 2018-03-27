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
     * @param string                       $status
     * @param null|string                  $order
     *
     * @return array
     */
    public function getProviderTariffs(ProviderEntity $provider, string $status = '', ?string $order = null): array
    {
        $qb = $this->createQueryBuilder('t')->select('t');
        $qb->andWhere('t.provider = :provider')->setParameter('provider', $provider);
        if (!empty($status)) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status);
        }

        if ($order !== null && in_array($order, ['asc', 'desc'])) {
            $qb->orderBy('t.cost', $order);
        } else {
            $qb->orderBy('t.id', 'DESC');
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
