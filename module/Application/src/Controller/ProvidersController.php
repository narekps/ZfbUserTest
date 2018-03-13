<?php

namespace Application\Controller;

use Application\Form\TariffForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ProviderRepository;
use Application\Form\NewProviderForm;
use Application\Entity\Tariff as TariffEntity;
use Application\Repository\TariffRepository;

/**
 * Class ProvidersController
 *
 * @package Application\Controller
 */
class ProvidersController extends AbstractActionController
{
    /**
     * @var NewProviderForm
     */
    private $newProviderForm;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var TariffRepository
     */
    private $tariffRepository;

    /**
     * ProvidersController constructor.
     *
     * @param \Application\Form\NewProviderForm          $newProviderForm
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\TariffRepository   $tariffRepository
     */
    public function __construct(NewProviderForm $newProviderForm, ProviderRepository $providerRepository, TariffRepository $tariffRepository)
    {
        $this->newProviderForm = $newProviderForm;
        $this->providerRepository = $providerRepository;
        $this->tariffRepository = $tariffRepository;
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
            'search'          => $search,
            'providers'       => $providers,
            'newProviderForm' => $this->newProviderForm,
        ]);

        return $viewModel;
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

        $tariffForm = new TariffForm();

        $tariffs = $this->tariffRepository->getList($status);

        $viewModel = new ViewModel([
            'tariffForm'   => $tariffForm,
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
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $viewModel = new ViewModel([
            'provider'  => $provider,
            'activeTab' => 'users'
        ]);

        return $viewModel;
    }
}
