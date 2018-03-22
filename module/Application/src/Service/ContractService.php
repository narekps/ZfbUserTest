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
}
