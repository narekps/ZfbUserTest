<?php

namespace Application\EventListener\UserService;

use Doctrine\ORM\EntityManagerInterface;
use ZfbUser\Service\Event\AddUserEvent;
use Application\Entity\User as UserEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Tracker as TrackerEntity;
use ZfbUser\EventProvider\EventResult;
use Application\Repository\ProviderRepository;
use Application\Repository\TrackerRepository;

/**
 * Class AddUserEventListener
 *
 * @package Application\EventListener\UserService
 */
class AddUserEventListener
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

        switch ($type) {
            case 'provider':
                $this->createProvider($user, $formData);
                break;
            case 'tracker':
                $this->createTracker($user, $formData);
                break;
            case 'user':
                $this->createUser($user, $formData);
                break;
        }

        return $eventResult;
    }

    /**
     * @param \Application\Entity\User $user
     * @param array                    $formData
     *l
     *
     * @return \Application\Entity\Provider
     */
    protected function createProvider(UserEntity $user, array $formData)
    {
        $provider = new ProviderEntity();
        $provider->setFullName($formData['fullName']);
        $provider->setPhone($formData['phone']);
        $provider->setEmail($formData['email']);
        $provider->setAddress($formData['address']);
        $provider->setContactPerson($formData['contactPerson']);
        $provider->setInn($formData['inn']);
        $provider->setKpp($formData['kpp']);
        $provider->setEtpContractNumber($formData['etpContractNumber']);
        $provider->setEtpContractDate(new \DateTime($formData['etpContractDate']));

        $user->setProvider($provider);

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
        $tracker->setFullName($formData['fullName']);
        $tracker->setPhone($formData['phone']);
        $tracker->setEmail($formData['email']);
        $tracker->setAddress($formData['address']);
        $tracker->setContactPerson($formData['contactPerson']);
        $tracker->setInn($formData['inn']);
        $tracker->setKpp($formData['kpp']);

        /** @var ProviderRepository $providerRep */
        $providerRep = $this->entityManager->getRepository(ProviderEntity::class);

        if (!empty($formData['trackingProviders'])) {
            /** @var ProviderEntity[] $providers */
            $providers = $providerRep->findBy(['id' => $formData['trackingProviders']]);
            foreach ($providers as $provider) {
                $tracker->addTrackingProvider($provider);
            }
        }

        $user->setTracker($tracker);

        $this->entityManager->persist($tracker);

        return $tracker;
    }

    /**
     * @param \Application\Entity\User $user
     * @param array                    $formData
     *
     * @return \Application\Entity\User
     */
    protected function createUser(UserEntity $user, array $formData)
    {

        if (!empty($formData['provider_id'])) {
            /** @var ProviderRepository $providerRep */
            $providerRep = $this->entityManager->getRepository(ProviderEntity::class);
            /** @var ProviderEntity $provider */
            $provider = $providerRep->findOneBy(['id' => intval($formData['provider_id'])]);
            if ($provider) {
                $user->setProvider($provider);
            }
        }

        if (!empty($formData['tracker_id'])) {
            /** @var TrackerRepository $trackerRep */
            $trackerRep = $this->entityManager->getRepository(TrackerEntity::class);
            /** @var TrackerEntity $tracker */
            $tracker = $trackerRep->findOneBy(['id' => intval($formData['tracker_id'])]);
            if ($tracker) {
                $user->setTracker($tracker);
            }
        }

        return $user;
    }
}
