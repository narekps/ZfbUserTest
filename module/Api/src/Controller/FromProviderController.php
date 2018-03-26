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
        /*$key = 'd140869e65a3d7911c90116a875f6982d21cfeffe0533f56ca04bc10599a8962467e421c3679fea10f8d9cda27424144630a';
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
            $session->offsetSet('from_provider_identifier', $provider->getIdentifier());

            return $this->forward()->dispatch(AuthenticationController::class, ['action' => 'authenticate']);
        } catch (\Exception $ex) {
            return $this->notFoundAction();
        }
    }
}
