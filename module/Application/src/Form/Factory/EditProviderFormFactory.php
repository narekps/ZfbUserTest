<?php

namespace Application\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Form\EditProviderForm;

/**
 * Class EditProviderFormFactory
 *
 * @package Application\Form\Factory
 */
class EditProviderFormFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Application\Form\EditProviderForm|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $form = new EditProviderForm();

        return $form;
    }
}
