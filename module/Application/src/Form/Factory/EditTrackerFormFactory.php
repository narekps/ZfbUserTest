<?php

namespace Application\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Form\EditTrackerForm;
use Application\Entity\Provider as ProviderEntity;

/**
 * Class EditTrackerFormFactory
 *
 * @package Application\Form\Factory
 */
class EditTrackerFormFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Form\EditTrackerForm|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var \Application\Repository\ProviderRepository $rep */
        $rep = $entityManager->getRepository(ProviderEntity::class);

        $providers = $rep->getForSelect();

        $form = new EditTrackerForm('', [], $providers);

        return $form;
    }
}
