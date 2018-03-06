<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ProviderRepository;

/**
 * Class ProvidersController
 *
 * @package Application\Controller
 */
class ProvidersController extends AbstractActionController
{
    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * ProvidersController constructor.
     *
     * @param \Application\Repository\ProviderRepository $providerRepository
     */
    public function __construct(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $providers = $this->providerRepository->getList();

        $viewModel = new ViewModel([
            'providers' => $providers,
        ]);

        return $viewModel;
    }
}
