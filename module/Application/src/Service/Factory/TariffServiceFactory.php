<?php

namespace Application\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\TariffService;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Contract as ContractEntity;
use Application\Repository\ContractRepository;

/**
 * Class TariffServiceFactory
 *
 * @package Application\Service\Factory
 */
class TariffServiceFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Service\TariffService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ContractRepository $contractRepository */
        $contractRepository = $entityManager->getRepository(ContractEntity::class);

        return new TariffService($entityManager, $contractRepository);
    }
}
