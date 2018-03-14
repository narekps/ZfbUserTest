<?php

namespace Application\Service;

use Application\Entity\Provider as ProviderEntity;
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
    public function update(ProviderEntity $provider, array $data)
    {
        $provider->setFullName($data['fullName']);
        $provider->setPhone($data['phone']);
        $provider->setEmail($data['email']);
        $provider->setAddress($data['address']);
        $provider->setContactPerson($data['contactPerson']);
        $provider->setInn($data['inn']);
        $provider->setKpp($data['kpp']);
        $provider->setEtpContractNumber($data['etpContractNumber']);
        $provider->setEtpContractDate(new \DateTime($data['etpContractDate']));

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
}
