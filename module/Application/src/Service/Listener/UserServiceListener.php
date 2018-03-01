<?php

namespace Application\Service\Listener;

use Doctrine\ORM\EntityManagerInterface;
use ZfbUser\Service\Event\AddUserEvent;
use Application\Entity\User as UserEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Tracker as TrackerEntity;
use ZfbUser\EventProvider\EventResult;

/**
 * Class UserServiceListener
 *
 * @package Application\Service\Listener
 */
class UserServiceListener
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * UserServiceListener constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \ZfbUser\Service\Event\AddUserEvent $addUserEvent
     *
     * @return \ZfbUser\EventProvider\EventResult
     * @throws \ZfbUser\Service\Exception\EventResultException
     */
    public function onAddUserPre(AddUserEvent $addUserEvent)
    {
        $eventResult = new EventResult(false);

        /** @var UserEntity $user */
        $user = $addUserEvent->getUser();
        $formData = $addUserEvent->getFormData();
        $type = $formData['type'];

        if ($type == 'provider') {
            $this->createProvider($user, $formData);
        } else {
            $this->createTracker($user, $formData);
        }

        return $eventResult;
    }

    /**
     * @param \Application\Entity\User $user
     * @param array                    $formData
     *
     * @return \Application\Entity\Provider
     */
    protected function createProvider(UserEntity $user, array $formData)
    {
        $provider = new ProviderEntity();
        $provider->setUser($user);
        $provider->setFullName($formData['fullName']);

        $this->entityManager->persist($provider);

        return $provider;
    }

    /**
     * @param \Application\Entity\User $user
     * @param array                    $formData
     *
     * @return \Application\Entity\Tracker
     */
    protected function createTracker(UserEntity $user, array $formData)
    {
        $tracker = new TrackerEntity();
        $tracker->setUser($user);

        $this->entityManager->persist($tracker);

        return $tracker;
    }
}
