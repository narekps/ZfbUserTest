<?php

namespace Application\EventListener\Navigation\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\EventListener\Navigation\RbacListener;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class RbacListenerFactory
 *
 * @package Application\EventListener\Navigation\Factory
 */
class RbacListenerFactory implements FactoryInterface
{

    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\EventListener\Navigation\RbacListener|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var AuthorizationService $authorizationService */
        $authorizationService = $container->get(AuthorizationService::class);

        return new RbacListener($authorizationService);
    }
}
