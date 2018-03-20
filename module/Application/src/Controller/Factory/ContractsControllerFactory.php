<?php

namespace Application\Controller\Factory;

use Application\Form\ContractForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\ContractsController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Service\ContractService;
use Application\Repository\ContractRepository;
use Application\Entity\Contract as ContractEntity;

/**
 * Class ContractsControllerFactory
 *
 * @package Application\Controller\Factory
 */
class ContractsControllerFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Controller\TariffsController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ContractService $contractService */
        $contractService = $container->get(ContractService::class);

        /** @var ContractRepository $contractRepository */
        $contractRepository = $entityManager->getRepository(ContractEntity::class);

        $contractForm = new ContractForm();

        return new ContractsController($contractService, $contractRepository, $contractForm);
    }
}
