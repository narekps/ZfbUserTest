<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use ZfbUser\Controller\Plugin;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\InvoiceService;
use Application\Form\InvoiceForm;
use Application\Repository\InvoiceRepository;
use Application\Entity\User as UserEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;
use Application\Entity\Tracker as TrackerEntity;
use Application\Repository\TrackerRepository;
use Application\Entity\Client as ClientEntity;
use Application\Repository\ClientRepository;

/**
 * Class InvoicesController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class InvoicesController extends AbstractActionController
{
    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var InvoiceForm
     */
    private $invoiceForm;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var TrackerRepository
     */
    private $trackerRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * InvoicesController constructor.
     *
     * @param \Application\Service\InvoiceService        $invoiceService
     * @param \Application\Repository\InvoiceRepository  $invoiceRepository
     * @param \Application\Form\InvoiceForm              $invoiceForm
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\TrackerRepository  $trackerRepository
     * @param \Application\Repository\ClientRepository   $clientRepository
     */
    public function __construct(InvoiceService $invoiceService, InvoiceRepository $invoiceRepository, InvoiceForm $invoiceForm, ProviderRepository $providerRepository, TrackerRepository $trackerRepository, ClientRepository $clientRepository)
    {
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceForm = $invoiceForm;
        $this->providerRepository = $providerRepository;
        $this->trackerRepository = $trackerRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Список счетов
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $status = $this->params()->fromQuery('status', '');

        $invoices = $this->invoiceRepository->getList();

        $viewModel = new ViewModel([
            'invoices'    => $invoices,
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    public function providerAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var ProviderEntity|null $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $this->invoiceForm->prepareForProvider($provider);

        $status = $this->params()->fromQuery('status', '');

        $invoices = $this->invoiceRepository->getProviderInvoices($provider, $status);

        $viewModel = new ViewModel([
            'provider'     => $provider,
            'invoices'     => $invoices,
            'invoiceForm'  => $this->invoiceForm,
            'activeTab'    => 'invoices',
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    public function trackerAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var TrackerEntity $tracker */
        $tracker = $this->trackerRepository->findOneBy(['id' => $id]);
        if ($tracker === null) {
            return $this->notFoundAction();
        }

        $status = $this->params()->fromQuery('status', '');

        $invoices = $this->invoiceRepository->getTrackerInvoices($tracker, $status);

        $viewModel = new ViewModel([
            'tracker'      => $tracker,
            'invoices'     => $invoices,
            'activeTab'    => 'invoices',
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    public function clientAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var ClientEntity $client */
        $client = $this->clientRepository->findOneBy(['id' => $id]);
        if ($client === null) {
            return $this->notFoundAction();
        }

        $status = $this->params()->fromQuery('status', '');

        $invoices = $this->invoiceRepository->findAll();

        $viewModel = new ViewModel([
            'client'       => $client,
            'invoices'     => $invoices,
            'activeTab'    => 'invoices',
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    /**
     * Скачивание счета
     */
    public function downloadAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        die(__METHOD__);
    }

    /**
     * Выставление счета провайдером
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function createAction()
    {
        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getProvider()) {
            $jsonModel->setVariable('message', 'Не указан сервис-провайдер');

            return $jsonModel;
        }

        $provider = $user->getProvider();
        $this->invoiceForm->prepareForProvider($provider);

        $this->invoiceForm->setData($this->params()->fromPost());
        if (!$this->invoiceForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->invoiceForm->getMessages());

            return $jsonModel;
        }
        $data = $this->invoiceForm->getData();

        try {
            $invoice = $this->invoiceService->create($provider, $data);

            $jsonModel->setVariable('invoice', $invoice);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
