<?php

namespace Application\Controller;

use ZfbUser\Controller\Plugin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\ContractService;
use Application\Form\ContractForm;
use Application\Repository\ContractRepository;
use Application\Entity\Contract as ContractEntity;
use Application\Entity\User as UserEntity;

/**
 * Class ContractsController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class ContractsController extends AbstractActionController
{
    /**
     * @var ContractService
     */
    private $contractService;

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var ContractForm
     */
    private $contractForm;

    /**
     * ContractsController constructor.
     *
     * @param \Application\Service\ContractService       $contractService
     * @param \Application\Repository\ContractRepository $contractRepository
     * @param \Application\Form\ContractForm             $contractForm
     */
    public function __construct(ContractService $contractService, ContractRepository $contractRepository, ContractForm $contractForm)
    {
        $this->contractService = $contractService;
        $this->contractRepository = $contractRepository;
        $this->contractForm = $contractForm;
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
        $contract = $this->contractRepository->findOneBy(['id' => $id]);
        if ($contract === null) {
            return $this->notFoundAction();
        }

        $jsonModel = new JsonModel([
            'success'  => true,
            'contract' => $contract,
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

        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getProvider()) {
            $jsonModel->setVariable('message', 'Не указан сервис-провайдер');

            return $jsonModel;
        }

        $post = $request->getPost();
        $this->contractForm->setData($post);
        if (!$this->contractForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->contractForm->getMessages());

            return $jsonModel;
        }
        $data = $this->contractForm->getData();

        $contract = null;
        $id = intval($this->params()->fromRoute('id', 0));
        if ($id == 0) {
            $id = intval($data['id']);
        }
        if ($id > 0) {
            $contract = $this->contractRepository->findOneBy(['id' => $id]);

            if ($contract === null) {
                return $this->notFoundAction();
            }
        } else {
            $contract = new ContractEntity();
        }

        try {
            $contract->setProvider($user->getProvider());
            $contract = $this->contractService->save($contract, $data);

            $jsonModel->setVariable('contract', $contract);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('hasError', true);
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
