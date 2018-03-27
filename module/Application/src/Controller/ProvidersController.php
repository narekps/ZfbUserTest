<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;
use ZfbUser\Controller\Plugin;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ProviderRepository;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ContractRepository;
use Application\Form\EditProviderForm;
use Application\Service\ProviderService;
use Application\Form\ProviderConfigForm;

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
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var Form
     */
    private $newUserForm;

    /**
     * @var EditProviderForm
     */
    private $editProviderForm;

    /**
     * @var ProviderConfigForm
     */
    private $configForm;

    /**
     * ProvidersController constructor.
     *
     * @param \Application\Service\ProviderService       $providerService
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\ContractRepository $contractRepository
     * @param \Zend\Form\Form                            $newUserForm
     * @param \Application\Form\EditProviderForm         $editProviderForm
     * @param \Application\Form\ProviderConfigForm       $configForm
     */
    public function __construct(
        ProviderService $providerService,
        ProviderRepository $providerRepository,
        ContractRepository $contractRepository,
        Form $newUserForm,
        EditProviderForm $editProviderForm,
        ProviderConfigForm $configForm
    )
    {
        $this->providerService = $providerService;
        $this->providerRepository = $providerRepository;
        $this->contractRepository = $contractRepository;
        $this->newUserForm = $newUserForm;
        $this->editProviderForm = $editProviderForm;
        $this->configForm = $configForm;
    }

    /**
     * Список сервис-провайдеров
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $search = $this->params()->fromQuery('search', '');

        $providers = $this->providerRepository->getList($search);

        $viewModel = new ViewModel([
            'search'           => $search,
            'providers'        => $providers,
            'newProviderForm'  => $this->newUserForm,
            'editProviderForm' => $this->editProviderForm,
            'configForm'       => $this->configForm,
        ]);

        return $viewModel;
    }

    /**
     * Карточка конкретного сервис-провайдера
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function infoAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $viewModel = new ViewModel([
            'provider'  => $provider,
            'contracts' => $this->contractRepository->getByProvider($provider),
            'activeTab' => 'info'
        ]);

        return $viewModel;
    }

    /**
     * Получение информации о провайдере
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function getAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $provider = $this->providerRepository->getById($id);
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
     * Получение настроек провайдера
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function getConfigAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $provider = $this->providerRepository->getById($id);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $jsonModel = new JsonModel([
            'success' => true,
            'config'  => $provider->getConfig(),
        ]);

        return $jsonModel;
    }

    /**
     * Обновление провайдера
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }
        $jsonModel = new JsonModel(['success' => false]);

        $this->editProviderForm->setData($this->params()->fromPost());
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
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }

    /**
     * Обновление настроек провайдера
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function updateConfigAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->getById($id);
        if ($provider === null) {
            return $this->notFoundAction();
        }
        $jsonModel = new JsonModel(['success' => false]);

        $this->configForm->setData($this->params()->fromPost());
        if (!$this->configForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->configForm->getMessages());

            return $jsonModel;
        }
        $data = $this->configForm->getData();

        try {
            $config = $this->providerService->updateConfig($provider->getConfig(), $data);
            $jsonModel->setVariable('config', $config);

            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
