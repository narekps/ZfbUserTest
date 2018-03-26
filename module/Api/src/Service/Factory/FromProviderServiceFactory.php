<?php

namespace Api\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Api\Service\FromProviderService;
use Application\Repository\ClientRepository;
use Application\Entity\Client as ClientEntity;
use ZfbUser\Adapter\AdapterInterface;

/**
 * Class FromProviderServiceFactory
 *
 * @package Api\Service\Factory
 */
class FromProviderServiceFactory implements FactoryInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     *
     * @return \Api\Service\FromProviderService|object
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var ClientRepository $clientRep */
        $clientRep = $entityManager->getRepository(ClientEntity::class);

        /** @var AdapterInterface $authAdapter */
        $authAdapter = $container->get('zfbuser_authentication_adapter');

        $config = $container->get('Config');

        if (empty($config['fake_user'])) {
            throw new \Exception('Config "fake_user" is required and can not be empty!');
        }
        $fakeUserConfig = $config['fake_user'];

        return new FromProviderService($entityManager, $clientRep, $authAdapter, $fakeUserConfig);
    }
}
