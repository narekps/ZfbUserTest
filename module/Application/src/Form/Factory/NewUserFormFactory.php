<?php

namespace Application\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

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
     * @return mixed|object
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');

        $type = $request->getQuery('type', null);
        if (!$type) {
            $type = $request->getPost('type');
        }

        $type = mb_strtolower($type);
        if (!in_array($type, ['provider', 'tracker'])) {
            throw new \Exception('type is wrong');
        }
        $type = ucfirst($type);
        $className = "Application\Form\New{$type}Form";

        return $container->get($className);
    }
}
