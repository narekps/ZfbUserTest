<?php

namespace Api\Controller;

use Api\Service\FromProviderService;
use Firebase\JWT\JWT;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use ZfbUser\Controller\Plugin;
use Application\Repository\ProviderRepository;
use ZfbUser\Controller\AuthenticationController;
use Zend\Session;

/**
 * Class FromProviderController
 *
 * @method bool isGranted(string $permission, mixed $context = null)
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 *
 * @package Application\Controller
 */
class FromProviderController extends AbstractActionController
{
    /**
     * @var FromProviderService
     */
    private $fromProviderService;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * FromProviderController constructor.
     *
     * @param \Api\Service\FromProviderService           $fromProviderService
     * @param \Application\Repository\ProviderRepository $providerRepository
     */
    public function __construct(FromProviderService $fromProviderService, ProviderRepository $providerRepository)
    {
        $this->fromProviderService = $fromProviderService;
        $this->providerRepository = $providerRepository;
    }

    public function indexAction()
    {
        /*$key = '57bbf349b3eacedba757c2a8fb84ab5f03d1e8ef753c2d67bbdb1c2b818aac4037ea740d191547132c19f45a80320b2b72b6';
        $token = [
            'client_info' => [
                'foo' => 'bar',
                //'inn' => '1234567890',
                //'kpp' => '123456789',
                //'full_name' => 'ООО Яблоко',
                //'address' => 'Россия г. Москва',
            ],
        ];
        $jwt = JWT::encode($token, $key, 'HS256');
        echo $jwt;
        die;*/

        $this->zfbAuthentication()->getAuthService()->clearIdentity();

        $identifier = $this->params()->fromRoute('identifier', null);
        $jwt = $this->params()->fromRoute('jwt', null);
        if (empty($identifier) || empty($jwt)) {
            return $this->notFoundAction();
        }

        $provider = $this->providerRepository->getByIdentifier($identifier);
        if (!$provider) {
            return $this->notFoundAction();
        }

        try {
            list($identity, $credential) = $this->fromProviderService->process($provider, $jwt);

            /** @var \Zend\Http\PhpEnvironment\Request $request */
            $request = $this->getRequest();
            $request->setPost(new Parameters([
                'identity'   => $identity,
                'credential' => $credential,
            ]));

            $session = new Session\Container('api');
            $session->offsetSet('from_provider_identifier', $provider->getConfig()->getIdentifier());

            return $this->forward()->dispatch(AuthenticationController::class, ['action' => 'authenticate']);
        } catch (\Exception $ex) {
            return $this->notFoundAction();
        }
    }
}
