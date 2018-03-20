<?php

namespace Application\Service;

use Application\Entity\Contract as ContractEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ContractService
 *
 * @package Application\Service
 */
class ContractService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ContractService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Application\Entity\Contract $contract
     * @param array                        $data
     *
     * @return \Application\Entity\Contract
     * @throws \Exception
     */
    public function save(ContractEntity $contract, array $data): ContractEntity
    {
        $contract->exchangeArray($data);

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($contract);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $contract;

    }
}
