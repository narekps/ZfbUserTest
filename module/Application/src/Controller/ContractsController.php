<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
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
     * Список договоров
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $contracts = $this->contractRepository->findAll();

        $viewModel = new ViewModel([
            'contracts'    => $contracts,
            'contractForm' => $this->contractForm,
        ]);

        return $viewModel;
    }
}
