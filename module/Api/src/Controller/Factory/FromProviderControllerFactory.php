<?php

namespace Api\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Api\Controller\FromProviderController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Repository\ProviderRepository;
use Application\Repository\ClientRepository;
use Application\Entity\Client as ClientEntity;
use Application\Entity\Provider as ProviderEntity;

/**
 * Class FromProviderControllerFactory
 *
 * @package Application\Controller\Factory
 */
class FromProviderControllerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Controller\ClientsController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ClientRepository $clientRep */
        $clientRep = $entityManager->getRepository(ClientEntity::class);

        /** @var ProviderRepository $providerRep */
        $providerRep = $entityManager->getRepository(ProviderEntity::class);

        return new FromProviderController($providerRep, $clientRep);
    }
}
