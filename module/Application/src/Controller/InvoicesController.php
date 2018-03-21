<?php

namespace Application\Controller;

use ZfbUser\Controller\Plugin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\InvoiceService;
use Application\Form\InvoiceForm;
use Application\Repository\InvoiceRepository;
use Application\Entity\Invoice as InvoiceEntity;
use Application\Entity\User as UserEntity;

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
     * InvoicesController constructor.
     *
     * @param \Application\Service\InvoiceService       $invoiceService
     * @param \Application\Repository\InvoiceRepository $invoiceRepository
     * @param \Application\Form\InvoiceForm             $invoiceForm
     */
    public function __construct(InvoiceService $invoiceService, InvoiceRepository $invoiceRepository, InvoiceForm $invoiceForm)
    {
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceForm = $invoiceForm;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function createAction()
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

        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getProvider()) {
            $jsonModel->setVariable('message', 'Не указан сервис-провайдер');

            return $jsonModel;
        }

        $provider = $user->getProvider();
        $this->invoiceForm->prepareForProvider($provider);

        $post = $request->getPost();
        $this->invoiceForm->setData($post);
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
            $jsonModel->setVariable('hasError', true);
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
