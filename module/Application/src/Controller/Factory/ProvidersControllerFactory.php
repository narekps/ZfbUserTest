<?php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\ProvidersController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;
use Application\Entity\Tariff as TariffEntity;
use Application\Entity\User as UserEntity;
use Application\Repository\TariffRepository;
use Application\Form\NewProviderForm;
use Application\Repository\UserRepository;

/**
 * Class ProvidersControllerFactory
 *
 * @package Application\Controller\Factory
 */
class ProvidersControllerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Controller\ProvidersController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ProviderRepository $providerRep */
        $providerRep = $entityManager->getRepository(ProviderEntity::class);

        /** @var TariffRepository $tariffRep */
        $tariffRep = $entityManager->getRepository(TariffEntity::class);

        /** @var UserRepository $userRepository */
        $userRepository = $container->get('zfbuser_user_repository');

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');

        /** @var \Zend\Mvc\Application $app */
        $app = $container->get('Application');
        $e = $app->getMvcEvent();
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch->getMatchedRouteName() == 'providers') {
            $action = $routeMatch->getParam('action');
            if ($action == 'users') {
                $request->getQuery()->set('type', 'user');
            } else {
                $request->getQuery()->set('type', 'provider');
            }
        }

        /** @var NewProviderForm $newUserForm */
        $newUserForm = $container->get('zfbuser_new_user_form');

        return new ProvidersController($newUserForm, $providerRep, $tariffRep, $userRepository);
    }
}
