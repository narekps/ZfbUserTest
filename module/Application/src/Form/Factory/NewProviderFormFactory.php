<?php

namespace Application\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfbUser\Options\ModuleOptions;
use Application\Form\NewProviderForm;

/**
 * Class NewProviderFormFactory
 *
 * @package Application\Form\Factory
 */
class NewProviderFormFactory implements FactoryInterface
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

        $form = new NewProviderForm($formOptions, $recaptchaOptions);

        return $form;
    }
}
