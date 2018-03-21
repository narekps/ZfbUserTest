<?php

namespace Application\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\InvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Contract as ContractEntity;
use Application\Entity\Client as ClientEntity;
use Application\Repository\ContractRepository;
use Application\Repository\ClientRepository;

/**
 * Class InvoiceServiceFactory
 *
 * @package Application\Service\Factory
 */
class InvoiceServiceFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Service\InvoiceService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ContractRepository $contractRepository */
        $contractRepository = $entityManager->getRepository(ContractEntity::class);

        /** @var ClientRepository $clientRepository */
        $clientRepository = $entityManager->getRepository(ClientEntity::class);

        return new InvoiceService($entityManager, $contractRepository, $clientRepository);
    }
}
