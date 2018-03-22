<?php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\UsersController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;
use Application\Entity\Tracker as TrackerEntity;
use Application\Repository\TrackerRepository;
use Application\Repository\UserRepository;
use Application\Form\NewUserForm;
use Application\Form\UpdateUserForm;


/**
 * Class UsersControllerFactory
 *
 * @package Application\Controller\Factory
 */
class UsersControllerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Controller\UsersController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var UserRepository $userRepository */
        $userRepository = $container->get('zfbuser_user_repository');

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $entityManager->getRepository(ProviderEntity::class);

        /** @var TrackerRepository $trackerRepository */
        $trackerRepository = $entityManager->getRepository(TrackerEntity::class);

        //TODO: fix it!
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');
        $request->getQuery()->set('type', 'user');

        /** @var NewUserForm $newUserForm */
        $newUserForm = $container->get('zfbuser_new_user_form');

        /** @var UpdateUserForm $updateUserForm */
        $updateUserForm = $container->get('zfbuser_update_user_form');

        return new UsersController($userRepository, $providerRepository, $trackerRepository, $newUserForm, $updateUserForm);
    }
}
