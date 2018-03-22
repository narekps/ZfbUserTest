<?php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\ClientsController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Repository\ClientRepository;
use Application\Entity\Client as ClientEntity;

/**
 * Class ClientsControllerFactory
 *
 * @package Application\Controller\Factory
 */
class ClientsControllerFactory implements FactoryInterface
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

        return new ClientsController($clientRep);
    }
}
