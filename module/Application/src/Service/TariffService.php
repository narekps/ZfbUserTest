<?php

namespace Application\Service;

use Application\Entity\Tariff as TariffEntity;
use Doctrine\ORM\EntityManagerInterface;

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
     * TariffService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Application\Entity\Tariff $tariff
     * @param array                      $data
     *
     * @return \Application\Entity\Tariff
     * @throws \Exception
     */
    public function save(TariffEntity $tariff, array $data)
    {
        $tariff->setName($data['name']);
        $tariff->setDescription($data['description']);
        $tariff->setCost(floatval($data['cost']));
        $tariff->setNds($data['nds']);
        $tariff->setSaleEndDate(new \DateTime($data['saleEndDate']));
        $tariff->setCurrency($data['currency']);

        $this->entityManager->beginTransaction();
        try {
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
