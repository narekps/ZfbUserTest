<?php

namespace Application\Controller;

use ZfbUser\Controller\Plugin;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Repository\ClientRepository;
use Application\Entity\Client as ClientEntity;

/**
 * Class ClientsController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class ClientsController extends AbstractActionController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * ClientsController constructor.
     *
     * @param \Application\Repository\ClientRepository $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Список клиентов
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $search = $this->params()->fromQuery('search', '');

        $clients = $this->clientRepository->getList($search);

        $viewModel = new ViewModel([
            'search'  => $search,
            'clients' => $clients,
        ]);

        return $viewModel;
    }

    /**
     * Карточка конкретного клиента
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function infoAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var ClientEntity $client */
        $client = $this->clientRepository->findOneBy(['id' => $id]);
        if ($client === null) {
            return $this->notFoundAction();
        }

        $viewModel = new ViewModel([
            'client'    => $client,
            'activeTab' => 'info'
        ]);

        return $viewModel;
    }
}
