<?php

namespace Api\Controller;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcRbac\Mvc\Controller\Plugin\IsGranted;
use ZfbUser\Controller\Plugin;
use Application\Entity\User as UserEntity;
use Application\Repository\ProviderRepository;
use Application\Repository\ClientRepository;
use Application\Entity\Client as ClientEntity;
use Application\Entity\Provider as ProviderEntity;

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
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * FromProviderController constructor.
     *
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\ClientRepository   $clientRepository
     */
    public function __construct(ProviderRepository $providerRepository, ClientRepository $clientRepository)
    {
        $this->providerRepository = $providerRepository;
        $this->clientRepository = $clientRepository;
    }

    public function indexAction()
    {
        $identifier = $this->params()->fromRoute('identifier', null);
        $jwt = $this->params()->fromRoute('jwt', null);

        if (empty($identifier) || empty($jwt)) {
            return $this->notFoundAction();
        }

        $provider = $this->providerRepository->getByIdentifier($identifier);

        echo json_encode($provider);
        die;
    }
}
