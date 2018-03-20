<?php

namespace Application\Form\Factory;

use Application\Form\UpdateUserForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfbUser\Options\ModuleOptions;

/**
 * Class UpdateUserFormFactory
 *
 * @package Application\Form\Factory
 */
class UpdateUserFormFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Form\UpdateUserForm|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);
        $formOptions = $moduleOptions->getUpdateUserFormOptions();
        $recaptchaOptions = $moduleOptions->getRecaptchaOptions();

        $form = new UpdateUserForm($formOptions, $recaptchaOptions);

        return $form;
    }
}
