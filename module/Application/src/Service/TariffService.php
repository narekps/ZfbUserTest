<?php

namespace Application\Service;

use Application\Entity\Tariff as TariffEntity;
use Doctrine\ORM\EntityManagerInterface;
use Application\Repository\ContractRepository;

/**
 * Class TariffService
 *
 * @package Application\Service
 */
class TariffService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ContractRepository
     */
    protected $contractRepository;

    /**
     * TariffService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface       $entityManager
     * @param \Application\Repository\ContractRepository $contractRepository
     */
    public function __construct(EntityManagerInterface $entityManager, ContractRepository $contractRepository)
    {
        $this->entityManager = $entityManager;
        $this->contractRepository = $contractRepository;
    }

    /**
     * @param \Application\Entity\Tariff $tariff
     * @param array                      $data
     *
     * @return \Application\Entity\Tariff
     * @throws \Exception
     */
    public function save(TariffEntity $tariff, array $data): TariffEntity
    {
        $this->entityManager->beginTransaction();
        try {
            $tariff->exchangeArray($data);

            $data['contract_id'] = intval($data['contract_id']);
            $contract = $this->contractRepository->getById($data['contract_id']);
            if ($contract === null) {
                throw new \Exception('Договор не найден');
            }
            $tariff->setContract($contract);

            $this->entityManager->persist($tariff);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $tariff;

    }
}
