<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Invoice as InvoiceEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Tracker as TrackerEntity;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

/**
 * Class InvoiceRepository
 *
 * @package Application\Repository
 */
class InvoiceRepository extends EntityRepository
{
    /**
     * @param array $queryParams
     *
     * @return \Application\Entity\Invoice[]
     */
    public function getList(array $queryParams = []): array
    {
        $qb = $this->createQueryBuilder('i')->select('i, p');
        $qb->leftJoin('i.payments', 'p');

        $this->applyQueryParams($qb, $queryParams);

        /** @var InvoiceEntity[] $invoices */
        $invoices = $qb->getQuery()->getResult();

        return $invoices;
    }

    /***
     * @param \Application\Entity\Provider $provider
     * @param array                        $queryParams
     *
     * @return array
     */
    public function getProviderInvoices(ProviderEntity $provider, array $queryParams = []): array
    {
        $qb = $this->createQueryBuilder('i')->select('i, p');
        $qb->leftJoin('i.payments', 'p');

        $qb->andWhere('i.provider = :provider')->setParameter('provider', $provider);

        $this->applyQueryParams($qb, $queryParams);

        /** @var InvoiceEntity[] $invoices */
        $invoices = $qb->getQuery()->getResult();

        return $invoices;
    }

    /***
     * @param \Application\Entity\Tracker $tracker
     * @param array                       $queryParams
     *
     * @return array
     */
    public function getTrackerInvoices(TrackerEntity $tracker, array $queryParams = []): array
    {
        $providersIds = [];
        /** @var ProviderEntity $provider */
        foreach ($tracker->getTrackingProviders() as $provider) {
            $providersIds[] = $provider->getId();
        }
        $qb = $this->createQueryBuilder('i')->select('i, p');
        $qb->leftJoin('i.payments', 'p');
        $qb->andWhere($qb->expr()->in('i.provider', $providersIds));
        if (!empty($status)) {
            $qb->andWhere('i.status = :status')->setParameter('status', $status);
        }

        $this->applyQueryParams($qb, $queryParams);

        /** @var InvoiceEntity[] $invoices */
        $invoices = $qb->getQuery()->getResult();

        return $invoices;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param array                      $queryParams
     */
    private function applyQueryParams(QueryBuilder &$qb, array $queryParams)
    {
        if (!empty($queryParams['client'])) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('lower(i.clientFullName)', "'%{$queryParams['client']}%'"),
                $qb->expr()->like('i.clientInn', "'%{$queryParams['client']}%'")
            ));
        }
        if (!empty($queryParams['name'])) {
            $qb->andWhere($qb->expr()->like('lower(i.name)', "'%{$queryParams['name']}%'"));
        }

        if (!empty($queryParams['invoice_date_from'])) {
            $qb->andWhere($qb->expr()->gte('i.invoiceDate', "'{$queryParams['invoice_date_from']}'"));
        }
        if (!empty($queryParams['invoice_date_to'])) {
            $qb->andWhere($qb->expr()->lte('i.invoiceDate', "'{$queryParams['invoice_date_to']}'"));
        }

        if (!empty($queryParams['sum'])) {
            $qb->andWhere($qb->expr()->eq('i.sum', floatval($queryParams['sum'])));
        }

        if (!empty($queryParams['number'])) {
            $qb->andWhere($qb->expr()->eq('i.id', intval($queryParams['number'])));
        }

        if (!empty($queryParams['status'])) {
            $qb->andWhere('i.status = :status')->setParameter('status', $queryParams['status']);
        }

        if (!empty($queryParams['sum_payment'])) {
            $qb->andWhere($qb->expr()->eq('p.sum', floatval($queryParams['sum_payment'])));
        }
    }
}
