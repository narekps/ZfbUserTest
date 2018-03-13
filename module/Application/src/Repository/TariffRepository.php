<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Tariff as TariffEntity;

/**
 * Class TariffRepository
 *
 * @package Application\Repository
 */
class TariffRepository extends EntityRepository
{

    /**
     * @param string $status Статус тарифа, пустая строка - все статусы
     *
     * @return \Application\Entity\Tariff[]
     */
    public function getList(string $status)
    {
        $qb = $this->createQueryBuilder('t')->select('t');
        if (!empty($status)) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status);
        }

        /** @var TariffEntity[] $tariffs */
        $tariffs = $qb->getQuery()->getResult();

        return $tariffs;
    }
}
