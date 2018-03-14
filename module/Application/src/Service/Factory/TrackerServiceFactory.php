<?php

namespace Application\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\TrackerService;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;

/**
 * Class TrackerServiceFactory
 *
 * @package Application\Service\Factory
 */
class TrackerServiceFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Service\TrackerService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $entityManager->getRepository(ProviderEntity::class);

        return new TrackerService($entityManager, $providerRepository);
    }
}
