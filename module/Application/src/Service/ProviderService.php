<?php

namespace Application\Service;

use Application\Entity\Provider as ProviderEntity;
use Application\Entity\ProviderConfig as ProviderConfigEntity;
use Application\Entity\Contract as ContractEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ProviderService
 *
 * @package Application\Service
 */
class ProviderService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * TariffService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Application\Entity\Provider $provider
     * @param array                        $data
     *
     * @return \Application\Entity\Provider
     * @throws \Exception
     */
    public function update(ProviderEntity $provider, array $data): ProviderEntity
    {
        $provider->exchangeArray($data);

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($provider);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $provider;
    }

    /**
     * @param \Application\Entity\ProviderConfig $config
     * @param array                              $data
     *
     * @return \Application\Entity\ProviderConfig
     * @throws \Exception
     */
    public function updateConfig(ProviderConfigEntity $config, array $data): ProviderConfigEntity
    {
        $config->exchangeArray($data);

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($config);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $config;
    }
}
