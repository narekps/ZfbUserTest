<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Invoice as InvoiceEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Tracker as TrackerEntity;

/**
 * Class InvoiceRepository
 *
 * @package Application\Repository
 */
class InvoiceRepository extends EntityRepository
{
    /**
     *
     * @return \Application\Entity\Invoice[]
     */
    public function getList(): array
    {
        $qb = $this->createQueryBuilder('i')->select('i');

        /** @var InvoiceEntity[] $invoices */
        $invoices = $qb->getQuery()->getResult();

        return $invoices;
    }

    /***
     * @param \Application\Entity\Provider $provider
     * @param string                       $status
     *
     * @return \Application\Entity\Invoice[]
     */
    public function getProviderInvoices(ProviderEntity $provider, string $status = ''): array
    {
        $qb = $this->createQueryBuilder('i')->select('i');
        $qb->andWhere('i.provider = :provider')->setParameter('provider', $provider);
        if (!empty($status)) {
            $qb->andWhere('i.status = :status')->setParameter('status', $status);
        }

        /** @var InvoiceEntity[] $invoices */
        $invoices = $qb->getQuery()->getResult();

        return $invoices;
    }

    /***
     * @param \Application\Entity\Tracker $tracker
     * @param string                      $status
     *
     * @return array
     */
    public function getTrackerInvoices(TrackerEntity $tracker, string $status = ''): array
    {
        $providersIds = [];
        /** @var ProviderEntity $provider */
        foreach($tracker->getTrackingProviders() as $provider){
            $providersIds[] = $provider->getId();
        }
        $qb = $this->createQueryBuilder('i')->select('i');
        $qb->andWhere($qb->expr()->in('i.provider', $providersIds));
        if (!empty($status)) {
            $qb->andWhere('i.status = :status')->setParameter('status', $status);
        }

        /** @var InvoiceEntity[] $invoices */
        $invoices = $qb->getQuery()->getResult();

        return $invoices;
    }
}
