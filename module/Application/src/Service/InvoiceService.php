<?php

namespace Application\Service;

use Application\Entity\Invoice as InvoiceEntity;
use Application\Entity\Provider as ProviderEntity;
use Application\Entity\Client as ClientEntity;
use Application\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Application\Repository\ContractRepository;

/**
 * Class InvoiceService
 *
 * @package Application\Service
 */
class InvoiceService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ContractRepository
     */
    protected $contractRepository;

    /**
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * InvoiceService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface       $entityManager
     * @param \Application\Repository\ContractRepository $contractRepository
     * @param \Application\Repository\ClientRepository   $clientRepository
     */
    public function __construct(EntityManagerInterface $entityManager, ContractRepository $contractRepository, ClientRepository $clientRepository)
    {
        $this->entityManager = $entityManager;
        $this->contractRepository = $contractRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param \Application\Entity\Provider $provider
     * @param array                        $data
     *
     * @return \Application\Entity\Invoice
     * @throws \Exception
     */
    public function create(ProviderEntity $provider, array $data): InvoiceEntity
    {
        $this->entityManager->beginTransaction();

        try {
            $data['type'] = InvoiceEntity::TYPE_CUSTOM_SERVICE;
            $data['providerFullName'] = $provider->getFullName();
            $data['providerInn'] = $provider->getInn();
            $data['providerKpp'] = $provider->getKpp();

            $client = $this->clientRepository->getByInnKpp($data['clientInn'], $data['clientKpp']);
            if ($client === null) {
                $client = new ClientEntity();
                $client->setFullName($data['clientFullName']);
                $client->setInn($data['clientInn']);
                $client->setKpp($data['clientKpp']);
                $client->setAddress($data['clientAddress']);
                $this->entityManager->persist($client);
            }

            $invoice = new InvoiceEntity();
            $invoice->exchangeArray($data);
            $invoice->setProvider($provider);
            $invoice->setClient($client);

            $data['contract_id'] = intval($data['contract_id']);
            $contract = $this->contractRepository->getById($data['contract_id']);
            if ($contract === null) {
                throw new \Exception('Договор не найден');
            }
            $invoice->setContract($contract);

            $this->entityManager->persist($invoice);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $ex) {
            $this->entityManager->rollback();

            throw $ex;
        }

        return $invoice;

    }
}
