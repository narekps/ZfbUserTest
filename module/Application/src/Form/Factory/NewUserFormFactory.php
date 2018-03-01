<?php

namespace Application\Form\Factory;

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

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');

        $defaultType = 'provider';
        $type = $request->getQuery('type', $defaultType);
        if (!in_array($type, [$defaultType, 'tracker'])) {
            $type = $defaultType;
        }
        $type = ucfirst($type);
        $className = "\Application\Form\New{$type}Form";

        /** @var \Zend\Form\Form $form */
        $form = new $className($formOptions, $recaptchaOptions);

        return $form;
    }
}
