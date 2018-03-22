<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;
use ZfbUser\Controller\Plugin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\TrackerRepository;
use Application\Entity\Tracker as TrackerEntity;
use Application\Repository\UserRepository;
use Application\Form\EditTrackerForm;
use Application\Service\TrackerService;

/**
 * Class TrackersController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class TrackersController extends AbstractActionController
{
    /**
     * @var TrackerService $tra
     */
    private $trackerService;

    /**
     * @var TrackerRepository
     */
    private $trackerRepository;

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
     * @var EditTrackerForm
     */
    private $editTrackerForm;

    /**
     * TrackersController constructor.
     *
     * @param \Application\Service\TrackerService       $trackerService
     * @param \Application\Repository\TrackerRepository $trackerRepository
     * @param \Application\Repository\UserRepository    $userRepository
     * @param \Zend\Form\Form                           $newUserForm
     * @param \Zend\Form\Form                           $updateUserForm
     * @param \Application\Form\EditTrackerForm         $editTrackerForm
     */
    public function __construct(
        TrackerService $trackerService,
        TrackerRepository $trackerRepository,
        UserRepository $userRepository,
        Form $newUserForm,
        Form $updateUserForm,
        EditTrackerForm $editTrackerForm
    )
    {
        $this->trackerService = $trackerService;
        $this->trackerRepository = $trackerRepository;
        $this->userRepository = $userRepository;
        $this->newUserForm = $newUserForm;
        $this->updateUserForm = $updateUserForm;
        $this->editTrackerForm = $editTrackerForm;
    }

    /**
     * Список контролирующих организаций
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $search = $this->params()->fromQuery('search', '');

        $trackers = $this->trackerRepository->getList($search);

        $viewModel = new ViewModel([
            'search'          => $search,
            'trackers'        => $trackers,
            'newTrackerForm'  => $this->newUserForm,
            'editTrackerForm' => $this->editTrackerForm,
        ]);

        return $viewModel;
    }

    /**
     * Карточка конкретной организации
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function infoAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $tracker = $this->trackerRepository->findOneBy(['id' => $id]);
        if ($tracker === null) {
            return $this->notFoundAction();
        }

        $viewModel = new ViewModel([
            'tracker'   => $tracker,
            'activeTab' => 'info'
        ]);

        return $viewModel;
    }

    /**
     * Получение информации об организации
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function getAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $tracker = $this->trackerRepository->findOneBy(['id' => $id]);
        if ($tracker === null) {
            return $this->notFoundAction();
        }

        $jsonModel = new JsonModel([
            'success'    => true,
            'contragent' => $tracker,
        ]);

        return $jsonModel;
    }

    /**
     * Обновление организации
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var TrackerEntity $tracker */
        $tracker = $this->trackerRepository->findOneBy(['id' => $id]);
        if ($tracker === null) {
            return $this->notFoundAction();
        }
        $jsonModel = new JsonModel(['success' => false]);

        $this->editTrackerForm->setData($this->params()->fromPost());
        if (!$this->editTrackerForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->editTrackerForm->getMessages());

            return $jsonModel;
        }
        $data = $this->editTrackerForm->getData();

        try {
            $tracker = $this->trackerService->update($tracker, $data);
            $jsonModel->setVariable('tracker', $tracker);

            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
