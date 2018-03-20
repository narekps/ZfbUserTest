<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;
use ZfbUser\Controller\Plugin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Application\Form\TariffForm;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ProviderRepository;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\TariffRepository;
use Application\Repository\UserRepository;
use Application\Form\EditProviderForm;
use Application\Service\ProviderService;

/**
 * Class ProvidersController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class ProvidersController extends AbstractActionController
{
    /**
     * @var ProviderService
     */
    private $providerService;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var TariffRepository
     */
    private $tariffRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Form
     */
    private $newUserForm;

    /**
     * @var Form
     */
    private $updateUserForm;

    /**
     * @var TariffForm
     */
    private $tariffForm;

    /**
     * @var EditProviderForm
     */
    private $editProviderForm;

    /**
     * ProvidersController constructor.
     *
     * @param \Application\Service\ProviderService       $providerService
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\TariffRepository   $tariffRepository
     * @param \Application\Repository\UserRepository     $userRepository
     * @param \Zend\Form\Form                            $newUserForm
     * @param \Zend\Form\Form                            $updateUserForm
     * @param \Application\Form\TariffForm               $tariffForm
     * @param \Application\Form\EditProviderForm         $editProviderForm
     */
    public function __construct(
        ProviderService $providerService,
        ProviderRepository $providerRepository,
        TariffRepository $tariffRepository,
        UserRepository $userRepository,
        Form $newUserForm,
        Form $updateUserForm,
        TariffForm $tariffForm,
        EditProviderForm $editProviderForm
    )
    {
        $this->providerService = $providerService;
        $this->providerRepository = $providerRepository;
        $this->tariffRepository = $tariffRepository;
        $this->userRepository = $userRepository;
        $this->newUserForm = $newUserForm;
        $this->updateUserForm = $updateUserForm;
        $this->tariffForm = $tariffForm;
        $this->editProviderForm = $editProviderForm;
    }


    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!empty($id)) {
            $this->redirect()->toRoute('providers');
        }

        $search = $this->params()->fromQuery('search', '');

        $providers = $this->providerRepository->getList($search);

        $viewModel = new ViewModel([
            'search'           => $search,
            'providers'        => $providers,
            'newProviderForm'  => $this->newUserForm,
            'editProviderForm' => $this->editProviderForm,
        ]);

        return $viewModel;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function getAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        /** @var Response $response */
        $response = $this->getResponse();
        if (!$request->isGet()) {
            $response->setStatusCode(Response::STATUS_CODE_405);

            return $response;
        }

        if (!$this->zfbAuthentication()->hasIdentity()) {
            $response->setStatusCode(Response::STATUS_CODE_403);

            return $response;
        }

        $id = intval($this->params()->fromRoute('id', 0));
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $jsonModel = new JsonModel([
            'success'    => true,
            'contragent' => $provider,
        ]);

        return $jsonModel;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function saveAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        /** @var Response $response */
        $response = $this->getResponse();
        if (!$request->isPost()) {
            $response->setStatusCode(Response::STATUS_CODE_405);

            return $response;
        }

        if (!$this->zfbAuthentication()->hasIdentity()) {
            $response->setStatusCode(Response::STATUS_CODE_403);

            return $response;
        }

        $id = intval($this->params()->fromRoute('id', 0));
        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }
        $jsonModel = new JsonModel(['success' => false]);

        $this->editProviderForm->setData($request->getPost());
        if (!$this->editProviderForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->editProviderForm->getMessages());

            return $jsonModel;
        }
        $data = $this->editProviderForm->getData();

        try {
            $provider = $this->providerService->update($provider, $data);
            $jsonModel->setVariable('provider', $provider);

            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('hasError', true);
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }

    public function infoAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $viewModel = new ViewModel([
            'provider'  => $provider,
            'activeTab' => 'info'
        ]);

        return $viewModel;
    }

    public function tariffsAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $status = $this->params()->fromQuery('status', '');

        $tariffs = $this->tariffRepository->getList($status);

        $viewModel = new ViewModel([
            'tariffForm'   => $this->tariffForm,
            'provider'     => $provider,
            'tariffs'      => $tariffs,
            'activeTab'    => 'tariffs',
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    public function usersAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $this->newUserForm->get('provider_id')->setValue($provider->getId());

        $users = $this->userRepository->getProviderUsers($provider);

        $viewModel = new ViewModel([
            'provider'       => $provider,
            'users'          => $users,
            'newUserForm'    => $this->newUserForm,
            'updateUserForm' => $this->updateUserForm,
            'activeTab'      => 'users'
        ]);

        return $viewModel;
    }
}
