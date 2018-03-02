<?php

namespace Application\EventListener\UserService;

use Doctrine\ORM\EntityManagerInterface;
use ZfbUser\Service\Event\AddUserEvent;
use Application\Entity\User as UserEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Tracker as TrackerEntity;
use ZfbUser\EventProvider\EventResult;
use Application\Repository\ProviderRepository;

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
     *l
     *
     * @return \Application\Entity\Provider
     */
    protected function createProvider(UserEntity $user, array $formData)
    {
        $provider = new ProviderEntity();
        $provider->setUser($user);
        $provider->setFullName($formData['fullName']);
        $provider->setInn($formData['inn']);
        $provider->setKpp($formData['kpp']);
        $provider->setEtpContractNumber($formData['etpContractNumber']);
        $provider->setEtpContractDate(new \DateTime($formData['etpContractDate']));

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

        /** @var ProviderRepository $providerRep */
        $providerRep = $this->entityManager->getRepository(ProviderEntity::class);

        /** @var ProviderEntity[] $providers */
        $providers = $providerRep->findBy(['id' => $formData['trackingProviders']]);
        foreach ($providers as $provider) {
            $tracker->addTrackingProvider($provider);
        }

        $this->entityManager->persist($tracker);

        return $tracker;
    }
}
