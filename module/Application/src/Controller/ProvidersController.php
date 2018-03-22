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
use Application\Repository\ContractRepository;
use Application\Repository\UserRepository;
use Application\Form\EditProviderForm;
use Application\Service\ProviderService;
use Application\Repository\InvoiceRepository;
use Application\Form\InvoiceForm;

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
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

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
     * @var InvoiceForm
     */
    private $invoiceForm;

    /**
     * ProvidersController constructor.
     *
     * @param \Application\Service\ProviderService       $providerService
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\TariffRepository   $tariffRepository
     * @param \Application\Repository\ContractRepository $contractRepository
     * @param \Application\Repository\InvoiceRepository  $invoiceRepository
     * @param \Application\Repository\UserRepository     $userRepository
     * @param \Zend\Form\Form                            $newUserForm
     * @param \Zend\Form\Form                            $updateUserForm
     * @param \Application\Form\TariffForm               $tariffForm
     * @param \Application\Form\EditProviderForm         $editProviderForm
     * @param \Application\Form\InvoiceForm              $invoiceForm
     */
    public function __construct(
        ProviderService $providerService,
        ProviderRepository $providerRepository,
        TariffRepository $tariffRepository,
        ContractRepository $contractRepository,
        InvoiceRepository $invoiceRepository,
        UserRepository $userRepository,
        Form $newUserForm,
        Form $updateUserForm,
        TariffForm $tariffForm,
        EditProviderForm $editProviderForm,
        InvoiceForm $invoiceForm
    )
    {
        $this->providerService = $providerService;
        $this->providerRepository = $providerRepository;
        $this->tariffRepository = $tariffRepository;
        $this->contractRepository = $contractRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->userRepository = $userRepository;
        $this->newUserForm = $newUserForm;
        $this->updateUserForm = $updateUserForm;
        $this->tariffForm = $tariffForm;
        $this->editProviderForm = $editProviderForm;
        $this->invoiceForm = $invoiceForm;
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
}
