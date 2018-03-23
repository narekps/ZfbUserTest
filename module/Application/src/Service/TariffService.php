<?php

namespace Application\Service;

use Application\Entity\Tariff as TariffEntity;
use Application\Entity\Provider as ProviderEntity;
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
     * @param \Application\Entity\Provider $provider
     * @param array                        $data
     *
     * @return \Application\Entity\Tariff
     * @throws \Exception
     */
    public function create(ProviderEntity $provider, array $data): TariffEntity
    {
        $this->entityManager->beginTransaction();
        try {
            $tariff = new TariffEntity();
            $tariff->exchangeArray($data);
            $tariff->setStatus(TariffEntity::STATUS_NEW);
            $tariff->setProvider($provider);

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

    /**
     * @param \Application\Entity\Tariff $tariff
     * @param array                      $data
     *
     * @return \Application\Entity\Tariff
     * @throws \Exception
     */
    public function update(TariffEntity $tariff, array $data): TariffEntity
    {
        $this->entityManager->beginTransaction();
        try {
            if (!$tariff->isEditable()) {
                throw new \Exception('Можно редактировать новые, действующие и отклоненные тарифы');
            }

            if ($tariff->getStatus() === TariffEntity::STATUS_ACTIVE) {
                $data['status'] = TariffEntity::STATUS_NEW;
            }

            if (!isset($data['published'])) {
                $data['published'] = 0;
            }

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

    /**
     * @param \Application\Entity\Tariff $tariff
     *
     * @return \Application\Entity\Tariff
     * @throws \Exception
     */
    public function archive(TariffEntity $tariff): TariffEntity
    {
        if ($tariff->getStatus() == TariffEntity::STATUS_PROCESSING) {
            throw new \Exception('Тариф в обработке - архивировать нельзя.');
        }
        try {
            $this->entityManager->beginTransaction();

            $tariff->setStatus(TariffEntity::STATUS_ARCHIVE);
            $tariff->setArchivedDate(new \DateTime());

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $tariff;
    }
}
