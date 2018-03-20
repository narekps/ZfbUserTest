<?php

namespace Application\Service;

use Application\Entity\Tracker as TrackerEntity;
use Application\Entity\Provider as ProviderEntity;
use Doctrine\ORM\EntityManagerInterface;
use Application\Repository\ProviderRepository;

/**
 * Class TrackerService
 *
 * @package Application\Service
 */
class TrackerService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    protected $providerRepository;

    /**
     * TrackerService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface       $entityManager
     * @param \Application\Repository\ProviderRepository $providerRepository
     */
    public function __construct(EntityManagerInterface $entityManager, ProviderRepository $providerRepository)
    {
        $this->entityManager = $entityManager;
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param \Application\Entity\Tracker $tracker
     * @param array                       $data
     *
     * @return \Application\Entity\Tracker
     * @throws \Exception
     */
    public function update(TrackerEntity $tracker, array $data): TrackerEntity
    {
        $tracker->exchangeArray($data);
        $tracker->getTrackingProviders()->clear();

        if (!empty($data['trackingProviders'])) {
            /** @var ProviderEntity[] $providers */
            $providers = $this->providerRepository->findBy(['id' => $data['trackingProviders']]);
            foreach ($providers as $provider) {
                $tracker->addTrackingProvider($provider);
            }
        }

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($tracker);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $tracker;

    }
}
