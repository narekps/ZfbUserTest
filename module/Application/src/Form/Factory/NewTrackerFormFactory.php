<?php

namespace Application\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfbUser\Options\ModuleOptions;
use Application\Form\NewTrackerForm;
use Application\Entity\Provider as ProviderEntity;

/**
 * Class NewTrackerFormFactory
 *
 * @package Application\Form\Factory
 */
class NewTrackerFormFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return object|\Zend\Form\Form
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);
        $formOptions = $moduleOptions->getNewUserFormOptions();
        $recaptchaOptions = $moduleOptions->getRecaptchaOptions();

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        /** @var \Application\Repository\ProviderRepository $rep */
        $rep = $entityManager->getRepository(ProviderEntity::class);
        $providers = $rep->getForSelect();

        $form = new NewTrackerForm($formOptions, $recaptchaOptions, $providers);

        return $form;
    }
}
