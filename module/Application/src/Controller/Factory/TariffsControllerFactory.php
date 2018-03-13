<?php

namespace Application\Controller\Factory;

use Application\Form\TariffForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\TariffsController;
use Doctrine\ORM\EntityManagerInterface;
use Application\Service\TariffService;
use Application\Repository\TariffRepository;
use Application\Entity\Tariff as TariffEntity;

/**
 * Class TariffsControllerFactory
 *
 * @package Application\Controller\Factory
 */
class TariffsControllerFactory implements FactoryInterface
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

        /** @var TariffService $tariffService */
        $tariffService = $container->get(TariffService::class);

        /** @var TariffRepository $tariffRepository */
        $tariffRepository = $entityManager->getRepository(TariffEntity::class);

        $tariffForm = new TariffForm();

        return new TariffsController($tariffService, $tariffRepository, $tariffForm);
    }
}
