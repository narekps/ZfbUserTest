<?php

namespace Application\EventListener\UserService\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\EventListener\UserService\AddUserEventListener;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AddUserEventListenerFactory
 *
 * @package Application\EventListener\UserService\Factory
 */
class AddUserEventListenerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\EventListener\UserService\AddUserEventListener|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $listener = new AddUserEventListener($entityManager);

        return $listener;
    }
}
