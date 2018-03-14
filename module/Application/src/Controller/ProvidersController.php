<?php

namespace Application\Controller;

use Application\Form\TariffForm;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ProviderRepository;
use Application\Entity\Tariff as TariffEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\TariffRepository;
use Application\Repository\UserRepository;

/**
 * Class ProvidersController
 *
 * @package Application\Controller
 */
class ProvidersController extends AbstractActionController
{
    /**
     * @var Form
     */
    private $newUserForm;

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
     * ProvidersController constructor.
     *
     * @param \Zend\Form\Form                            $newUserForm
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\TariffRepository   $tariffRepository
     * @param \Application\Repository\UserRepository     $userRepository
     */
    public function __construct(Form $newUserForm, ProviderRepository $providerRepository, TariffRepository $tariffRepository, UserRepository $userRepository)
    {
        $this->newUserForm = $newUserForm;
        $this->providerRepository = $providerRepository;
        $this->tariffRepository = $tariffRepository;
        $this->userRepository = $userRepository;
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
            'newProviderForm' => $this->newUserForm,
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
        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $this->newUserForm->get('provider_id')->setValue($provider->getId());

        $users = $this->userRepository->getProviderUsers($provider);

        $viewModel = new ViewModel([
            'provider'    => $provider,
            'users'       => $users,
            'newUserForm' => $this->newUserForm,
            'activeTab'   => 'users'
        ]);

        return $viewModel;
    }
}
