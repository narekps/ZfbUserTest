<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use ZfbUser\Controller\Plugin;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\TariffService;
use Application\Form\TariffForm;
use Application\Repository\TariffRepository;
use Application\Entity\Tariff as TariffEntity;
use Application\Entity\User as UserEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ProviderRepository;
use Application\Entity\Client as ClientEntity;
use Application\Repository\ClientRepository;

/**
 * Class TariffsController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 *
 * @package Application\Controller
 */
class TariffsController extends AbstractActionController
{
    /**
     * @var TariffService
     */
    private $tariffService;

    /**
     * @var TariffRepository
     */
    private $tariffRepository;

    /**
     * @var TariffForm
     */
    private $tariffForm;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * TariffsController constructor.
     *
     * @param \Application\Service\TariffService         $tariffService
     * @param \Application\Repository\TariffRepository   $tariffRepository
     * @param \Application\Form\TariffForm               $tariffForm
     * @param \Application\Repository\ProviderRepository $providerRepository
     * @param \Application\Repository\ClientRepository   $clientRepository
     */
    public function __construct(TariffService $tariffService, TariffRepository $tariffRepository, TariffForm $tariffForm, ProviderRepository $providerRepository, ClientRepository $clientRepository)
    {
        $this->tariffService = $tariffService;
        $this->tariffRepository = $tariffRepository;
        $this->tariffForm = $tariffForm;
        $this->providerRepository = $providerRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if ($user->getProvider()) {
            return $this->forward()->dispatch(self::class, ['action' => 'provider', 'id' => $user->getProvider()->getId()]);
        }

        if ($user->getClient()) {
            return $this->forward()->dispatch(self::class, ['action' => 'client', 'id' => $user->getClient()->getId()]);
        }

        return $this->notFoundAction();
    }

    /**
     * Список тарифов провайдера
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function providerAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var ProviderEntity $provider */
        $provider = $this->providerRepository->findOneBy(['id' => $id]);
        if ($provider === null) {
            return $this->notFoundAction();
        }

        $status = $this->params()->fromQuery('status', '');
        $tariffs = $this->tariffRepository->getProviderTariffs($provider, $status);

        $this->tariffForm->prepareForProvider($provider);

        $viewModel = new ViewModel([
            'tariffForm'   => $this->tariffForm,
            'provider'     => $provider,
            'tariffs'      => $tariffs,
            'activeTab'    => 'tariffs',
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    /**
     * Список тарифов для клиента
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function clientAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));

        /** @var ClientEntity $client */
        $client = $this->clientRepository->findOneBy(['id' => $id]);
        if ($client === null) {
            return $this->notFoundAction();
        }

        $provider = $client->getProvider();

        $status = $this->params()->fromQuery('status', '');
        $tariffs = $this->tariffRepository->getProviderTariffs($provider, $status);

        $viewModel = new ViewModel([
            'client'       => $client,
            'tariffs'      => $tariffs,
            'activeTab'    => 'tariffs',
            'activeStatus' => $status,
        ]);

        return $viewModel;
    }

    /**
     * Получение информации о тарифе
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function getAction()
    {
        $id = intval($this->params()->fromRoute('id', 0));
        $tariff = $this->tariffRepository->findOneBy(['id' => $id]);
        if ($tariff === null) {
            return $this->notFoundAction();
        }

        $jsonModel = new JsonModel([
            'success' => true,
            'tariff'  => $tariff,
        ]);

        return $jsonModel;
    }

    /**
     * Создание нового тарифа
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function createAction()
    {
        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getProvider()) {
            $jsonModel->setVariable('message', 'Не указан сервис-провайдер');

            return $jsonModel;
        }

        $provider = $user->getProvider();
        $this->tariffForm->prepareForProvider($provider);

        $this->tariffForm->setData($this->params()->fromPost());
        if (!$this->tariffForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->tariffForm->getMessages());

            return $jsonModel;
        }
        $data = $this->tariffForm->getData();

        try {
            $tariff = $this->tariffService->create($provider, $data);

            $jsonModel->setVariable('tariff', $tariff);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }

    /**
     * Обновление существующего тарифа
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getProvider()) {
            $jsonModel->setVariable('message', 'Не указан сервис-провайдер');

            return $jsonModel;
        }

        $provider = $user->getProvider();
        $this->tariffForm->prepareForProvider($provider);

        $this->tariffForm->setData($this->params()->fromPost());
        if (!$this->tariffForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->tariffForm->getMessages());

            return $jsonModel;
        }
        $data = $this->tariffForm->getData();

        $id = intval($this->params()->fromRoute('id', 0));

        /** @var TariffEntity $tariff */
        $tariff = $this->tariffRepository->findOneBy(['id' => $id]);
        if ($tariff === null) {
            return $this->notFoundAction();
        }

        try {
            $tariff = $this->tariffService->update($tariff, $data);

            $jsonModel->setVariable('tariff', $tariff);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }

    /**
     * Архивирование тарифа серсив-провайдером
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function archiveAction()
    {
        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getProvider()) {
            $jsonModel->setVariable('message', 'Не указан сервис-провайдер');

            return $jsonModel;
        }

        $id = intval($this->params()->fromRoute('id', 0));
        $tariff = $this->tariffRepository->getById($id);
        if ($tariff === null) {
            return $this->notFoundAction();
        }

        try {
            $tariff = $this->tariffService->archive($tariff);

            $jsonModel->setVariable('tariff', $tariff);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }

    /**
     * Покупка тарифа клиентом
     *
     * @return \Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function payAction()
    {
        $jsonModel = new JsonModel(['success' => false]);

        /** @var UserEntity $user */
        $user = $this->zfbAuthentication()->getIdentity();
        if (!$user->getClient()) {
            $jsonModel->setVariable('message', 'Не указан клиент.');

            return $jsonModel;
        }

        $id = intval($this->params()->fromRoute('id', 0));
        $tariff = $this->tariffRepository->getById($id);
        if ($tariff === null) {
            return $this->notFoundAction();
        }

        try {
            //TODO: fix it!
            //$tariff = $this->tariffService->pay($tariff);

            $jsonModel->setVariable('tariff', $tariff);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
