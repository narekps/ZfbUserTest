<?php

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\TrackersController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Tracker as TrackerEntity;
use Application\Repository\TrackerRepository;
use Application\Form\NewTrackerForm;
use Application\Repository\UserRepository;
use Application\Form\EditTrackerForm;
use Application\Service\TrackerService;
use Application\Form\UpdateUserForm;

/**
 * Class TrackersControllerFactory
 *
 * @package Application\Controller\Factory
 */
class TrackersControllerFactory implements FactoryInterface
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

        /** @var TrackerRepository $trackerRep */
        $trackerRep = $entityManager->getRepository(TrackerEntity::class);

        /** @var UserRepository $userRepository */
        $userRepository = $container->get('zfbuser_user_repository');

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');

        /** @var \Zend\Mvc\Application $app */
        $app = $container->get('Application');
        $e = $app->getMvcEvent();
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch->getMatchedRouteName() == 'trackers') {
            $action = $routeMatch->getParam('action');
            if ($action == 'users') {
                $request->getQuery()->set('type', 'user');
            } else {
                $request->getQuery()->set('type', 'tracker');
            }
        }

        //TODO: fix it!
        $request->getQuery()->set('type', 'tracker');

        /** @var NewTrackerForm $newUserForm */
        $newUserForm = $container->get('zfbuser_new_user_form');

        /** @var UpdateUserForm $updateUserForm */
        $updateUserForm = $container->get('zfbuser_update_user_form');

        /** @var EditTrackerForm $editTrackerForm */
        $editTrackerForm = $container->get(EditTrackerForm::class);

        /** @var TrackerService $trackerService */
        $trackerService = $container->get(TrackerService::class);

        return new TrackersController($trackerService, $trackerRep, $userRepository, $newUserForm, $updateUserForm, $editTrackerForm);
    }
}
