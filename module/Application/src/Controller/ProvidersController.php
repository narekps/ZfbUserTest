<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ProviderRepository;
use Application\Form\NewProviderForm;

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
     * @var NewProviderForm
     */
    private $newProviderForm;

    /**
     * ProvidersController constructor.
     *
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Form\NewProviderForm          $newProviderForm
     */
    public function __construct(ProviderRepository $providerRepository, NewProviderForm $newProviderForm)
    {
        $this->providerRepository = $providerRepository;
        $this->newProviderForm = $newProviderForm;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $search = $this->params()->fromQuery('search', '');

        $providers = $this->providerRepository->getList($search);

        $viewModel = new ViewModel([
            'search'          => $search,
            'providers'       => $providers,
            'newProviderForm' => $this->newProviderForm,
        ]);

        return $viewModel;
    }
}
