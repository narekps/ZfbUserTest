<?php

namespace Application\Service\Listener\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\Listener\UserServiceListener;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserServiceListenerFactory
 *
 * @package Application\Service\Listener\Factory
 */
class UserServiceListenerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Service\Listener\UserServiceListener|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $listener = new UserServiceListener($entityManager);

        return $listener;
    }
}
