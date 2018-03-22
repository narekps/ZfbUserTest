<?php

namespace Application\Controller;

use Zend\Form\Form;
use Zend\View\Model\ViewModel;
use ZfbUser\Controller\Plugin;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;
use Application\Entity\Tracker as TrackerEntity;
use Application\Repository\TrackerRepository;
use Application\Repository\UserRepository;

/**
 * Class UsersController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class UsersController extends AbstractActionController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var TrackerRepository
     */
    private $trackerRepository;

    /**
     * @var Form
     */
    private $newUserForm;

    /**
     * @var Form
     */
    private $updateUserForm;

    /**
     * UsersController constructor.
     *
     * @param \Application\Repository\UserRepository     $userRepository
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\TrackerRepository  $trackerRepository
     * @param \Zend\Form\Form                            $newUserForm
     * @param \Zend\Form\Form                            $updateUserForm
     */
    public function __construct(UserRepository $userRepository, ProviderRepository $providerRepository, TrackerRepository $trackerRepository, Form $newUserForm, Form $updateUserForm)
    {
        $this->userRepository = $userRepository;
        $this->providerRepository = $providerRepository;
        $this->trackerRepository = $trackerRepository;
        $this->newUserForm = $newUserForm;
        $this->updateUserForm = $updateUserForm;
    }

    /**
     * Список пользователей
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $users = $this->userRepository->findAll();

        $viewModel = new ViewModel([
            'users' => $users,
        ]);

        return $viewModel;
    }

    public function providerAction()
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
            'provider'       => $provider,
            'users'          => $users,
            'newUserForm'    => $this->newUserForm,
            'updateUserForm' => $this->updateUserForm,
            'activeTab'      => 'users',
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

        $this->newUserForm->get('tracker_id')->setValue($tracker->getId());

        $users = $this->userRepository->getTrackerUsers($tracker);

        $viewModel = new ViewModel([
            'tracker'        => $tracker,
            'users'          => $users,
            'newUserForm'    => $this->newUserForm,
            'updateUserForm' => $this->updateUserForm,
            'activeTab'      => 'users',
        ]);

        return $viewModel;
    }
}
