<?php

namespace Api\Service;

use Doctrine\ORM\EntityManagerInterface;
use Application\Repository\ClientRepository;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Client as ClientEntity;
use Application\Entity\User as UserEntity;
use Firebase\JWT\JWT;
use ZfbUser\Adapter\AdapterInterface;

/**
 * Class FromProviderService
 *
 * @package Api\Service
 */
class FromProviderService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * @var AdapterInterface
     */
    protected $authAdapter;

    /**
     * @var array
     */
    protected $fakeUserConfig;

    /**
     * FromProviderService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface     $entityManager
     * @param \Application\Repository\ClientRepository $clientRepository
     * @param \ZfbUser\Adapter\AdapterInterface        $authAdapter
     * @param array                                    $fakeUserConfig
     */
    public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository, AdapterInterface $authAdapter, array $fakeUserConfig)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->authAdapter = $authAdapter;
        $this->fakeUserConfig = $fakeUserConfig;
    }

    /**
     * Обработка перехода клиента от провайдера.
     * Если передан инн и кпп клиента, заводит нового юзера и клиента(если нету) и возвращаем логин/пароль.
     * Если инн и кпп не передан, возвращаем логин/пароль фейкового пользователя.
     *
     * @param \Application\Entity\Provider $provider Провайдер, от которого перешли в блиллинг
     * @param string                       $jwt      Токет от провайдера
     *
     * @return array [identity, credential] - Логин и пароль для аутентификации
     * @throws \Exception
     */
    public function process(ProviderEntity $provider, string $jwt)
    {
        try {
            $data = json_decode(json_encode(JWT::decode($jwt, $provider->getPrivateKey(), ['HS256'])), true);

            if (empty($data['client_info']['inn']) || empty($data['client_info']['kpp'])) {
                $identity = $this->fakeUserConfig['identity'];
                $credential = $this->fakeUserConfig['credential'];
            } else {
                $client = $this->clientRepository->getByInnKpp($data['client_info']['inn'], $data['client_info']['kpp']);
                $identity = 'fake_' . $data['client_info']['inn'] . '_' . $data['client_info']['kpp'];
                $credential = $this->fakeUserConfig['credential'];
                if ($client === null) {
                    $client = new ClientEntity();
                    $client->setInn($data['client_info']['inn']);
                    $client->setKpp($data['client_info']['kpp']);
                    $client->setFullName($data['client_info']['full_name'] ?? '');
                    $client->setAddress($data['client_info']['address'] ?? '');
                    $client->setDateCreated(new \DateTime());

                    $user = new UserEntity();
                    $user->setClient($client);
                    $user->setSurname('')->setName('Гость');
                    $user->setRole('client_user');

                    $user->setIdentity($identity);
                    $user->setIdentityConfirmed(true);
                    $user->setCredential($this->authAdapter->cryptCredential($credential));

                    $this->entityManager->beginTransaction();

                    $this->entityManager->persist($client);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $this->entityManager->commit();
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        return [$identity, $credential];
    }
}
