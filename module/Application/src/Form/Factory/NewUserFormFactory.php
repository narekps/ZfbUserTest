<?php

namespace Application\Form\Factory;

use Application\Form\NewUserForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfbUser\Options\ModuleOptions;

/**
 * Class NewUserFormFactory
 *
 * @package Application\Form\Factory
 */
class NewUserFormFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Form\NewUserForm|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);
        $formOptions = $moduleOptions->getNewUserFormOptions();
        $recaptchaOptions = $moduleOptions->getRecaptchaOptions();

        $form = new NewUserForm($formOptions, $recaptchaOptions);

        return $form;
    }
}
