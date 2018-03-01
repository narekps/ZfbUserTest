<?php

namespace Application\Service\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Zend\EventManager\Event;
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
     * @param \Zend\EventManager\Event $event
     *
     * @return \ZfbUser\EventProvider\EventResult
     * @throws \ZfbUser\Service\Exception\EventResultException
     */
    public function onAddUserBeginTransaction(Event $event)
    {
        $eventResult = new EventResult(false);

        /** @var UserEntity $user */
        $user = $event->getParam('user');
        $data = $event->getParam('data');
        $type = $data['type'];

        if ($type == 'provider') {
            $this->createProvider($data, $user);
        } else {
            $this->createTracker($data, $user);
        }

        return $eventResult;
    }

    /**
     * @param array                    $data
     * @param \Application\Entity\User $user
     *
     * @return \Application\Entity\Provider
     */
    protected function createProvider(array $data, UserEntity $user)
    {
        $provider = new ProviderEntity();
        $provider->setUser($user);
        $provider->setFullName($data['fullName']);

        $this->entityManager->persist($provider);

        return $provider;
    }

    /**
     * @param array                    $data
     * @param \Application\Entity\User $user
     *
     * @return \Application\Entity\Tracker
     */
    protected function createTracker(array $data, UserEntity $user)
    {
        $tracker = new TrackerEntity();
        $tracker->setUser($user);

        $this->entityManager->persist($tracker);

        return $tracker;
    }
}
