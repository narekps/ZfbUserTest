<?php

namespace Application\Controller\Factory;

use Application\Form\InvoiceForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\InvoicesController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Service\InvoiceService;
use Application\Repository\InvoiceRepository;
use Application\Entity\Invoice as InvoiceEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;
use Application\Entity\Tracker as TrackerEntity;
use Application\Repository\TrackerRepository;
use Application\Entity\Client as ClientEntity;
use Application\Repository\ClientRepository;

/**
 * Class InvoicesControllerFactory
 *
 * @package Application\Controller\Factory
 */
class InvoicesControllerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Controller\InvoicesController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var InvoiceService $invoiceService */
        $invoiceService = $container->get(InvoiceService::class);

        /** @var InvoiceRepository $invoiceRepository */
        $invoiceRepository = $entityManager->getRepository(InvoiceEntity::class);

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $entityManager->getRepository(ProviderEntity::class);

        /** @var TrackerRepository $trackerRepository */
        $trackerRepository = $entityManager->getRepository(TrackerEntity::class);

        /** @var ClientRepository $clientRepository */
        $clientRepository = $entityManager->getRepository(ClientEntity::class);

        $invoiceForm = $container->get(InvoiceForm::class);

        return new InvoicesController($invoiceService, $invoiceRepository, $invoiceForm, $providerRepository, $trackerRepository, $clientRepository);
    }
}
