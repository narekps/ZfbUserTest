<?php

namespace Application\Controller;

use ZfbUser\Controller\Plugin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\TariffService;
use Application\Form\TariffForm;
use Application\Repository\TariffRepository;
use Application\Entity\Tariff as TariffEntity;
use Application\Entity\User as UserEntity;

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
     * TariffsController constructor.
     *
     * @param \Application\Service\TariffService       $tariffService
     * @param \Application\Repository\TariffRepository $tariffRepository
     * @param \Application\Form\TariffForm             $tariffForm
     */
    public function __construct(TariffService $tariffService, TariffRepository $tariffRepository, TariffForm $tariffForm)
    {
        $this->tariffService = $tariffService;
        $this->tariffRepository = $tariffRepository;
        $this->tariffForm = $tariffForm;
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

        $provider = $user->getProvider();
        $this->tariffForm->prepareForProvider($provider);

        $post = $request->getPost();
        $this->tariffForm->setData($post);
        if (!$this->tariffForm->isValid()) {
            $jsonModel->setVariable('formErrors', $this->tariffForm->getMessages());

            return $jsonModel;
        }
        $data = $this->tariffForm->getData();

        $tariff = null;
        $id = intval($this->params()->fromRoute('id', 0));
        if ($id == 0) {
            $id = intval($data['id']);
        }
        if ($id > 0) {
            /** @var TariffEntity $tariff */
            $tariff = $this->tariffRepository->findOneBy(['id' => $id]);

            if ($tariff === null) {
                return $this->notFoundAction();
            }
        } else {
            $tariff = new TariffEntity();
            $tariff->setStatus(TariffEntity::STATUS_NEW);
            $tariff->setProvider($provider);
        }

        try {
            $tariff = $this->tariffService->save($tariff, $data);

            $jsonModel->setVariable('tariff', $tariff);
            $jsonModel->setVariable('success', true);
        } catch (\Exception $ex) {
            $jsonModel->setVariable('hasError', true);
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\JsonModel|\Zend\View\Model\ViewModel
     */
    public function archiveAction()
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
            $jsonModel->setVariable('hasError', true);
            $jsonModel->setVariable('message', $ex->getMessage());
        }

        return $jsonModel;
    }
}
