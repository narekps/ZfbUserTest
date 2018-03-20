<?php

namespace Application\Form\Factory;

use Application\Form\TariffForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Repository\ContractRepository;
use Application\Entity\Contract as ContractEntity;

/**
 * Class TariffFormFactory
 *
 * @package Application\Form\Factory
 */
class TariffFormFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Form\TariffForm|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ContractRepository $contractRepository */
        $contractRepository = $entityManager->getRepository(ContractEntity::class);

        $form = new TariffForm($contractRepository);

        return $form;
    }
}
