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

        $invoiceForm = $container->get(InvoiceForm::class);

        return new InvoicesController($invoiceService, $invoiceRepository, $invoiceForm);
    }
}
