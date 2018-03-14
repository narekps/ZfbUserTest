<?php

namespace Application\Repository\Factory;

use Application\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use ZfbUser\Options\ModuleOptions;

/**
 * Class UserRepositoryFactory
 *
 * @package Application\Repository\Factory
 */
class UserRepositoryFactory implements FactoryInterface
{

    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Repository\UserRepository|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $classMetadata = $entityManager->getClassMetadata($moduleOptions->getUserEntityClass());
        $repository = new UserRepository($entityManager, $classMetadata, $moduleOptions);

        return $repository;
    }
}
