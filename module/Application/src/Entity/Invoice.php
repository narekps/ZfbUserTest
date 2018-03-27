<?php

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Счет
 *
 * @ORM\Entity(repositoryClass="Application\Repository\InvoiceRepository")
 * @ORM\Table(name="invoices")
 */
class Invoice implements ArraySerializableInterface,\JsonSerializable
{
    const TYPE_TARIFF = 'tariff'; // Счет за покупку тарифа
    const TYPE_CUSTOM_SERVICE = 'customer_service'; // Счет за нетарифицированную услуг

    const STATUS_PAID = 'paid'; // Оплачен
    const STATUS_NOT_PAID = 'not_paid'; // Не оплачен

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PAID     => 'Оплачен',
            self::STATUS_NOT_PAID => 'Не оплачен',
        ];
    }

    /**
     * Идентификатор счета
     *
     * @var int
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Provider
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $provider;

    /**
     * @var Contract
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Contract")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id", nullable=false)
     */
    protected $contract;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $client;

    /**
     * @var \Application\Entity\Payment[]|\Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\Payment", mappedBy="invoice")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $payments;

    /**
     * Тим счета
     *
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    protected $type;

    /**
     * Статус оплаты
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    protected $status = self::STATUS_NOT_PAID;

    /**
     * Дата выставления счета
     *
     * @var \DateTime
     *
     * @ORM\Column(name="invoice_date", type="date", nullable=false)
     */
    protected $invoiceDate;

    /**
     * Наименование услуги/тарифа
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=1000, nullable=false)
     */
    protected $name;

    /**
     * Сумма
     *
     * @var float
     *
     * @ORM\Column(name="sum", type="float", nullable=false)
     */
    protected $sum;

    /**
     * Валюта
     *
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false, options={"fixed": true})
     */
    protected $currency;

    /**
     * Ставка НДС
     *
     * @var int|null
     *
     * @ORM\Column(name="nds", type="integer", nullable=true)
     */
    protected $nds;

    /**
     * Дата и время создания
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false)
     */
    protected $createdDate;

    /**
     * Полное юридическое наименование провайдера
     *
     * @var string
     *
     * @ORM\Column(name="provider_full_name", type="string", length=1024, nullable=false)
     */
    protected $providerFullName;

    /**
     * ИНН провайдера
     *
     * @var string
     *
     * @ORM\Column(name="provider_inn", type="string", length=12, nullable=false)
     */
    protected $providerInn;

    /**
     * КПП провайдера
     *
     * @var string
     *
     * @ORM\Column(name="provider_kpp", type="string", length=9, nullable=false)
     */
    protected $providerKpp;

    /**
     * Полное юридическое наименование плательщика
     *
     * @var string
     *
     * @ORM\Column(name="client_full_name", type="string", length=1024, nullable=false)
     */
    protected $clientFullName;

    /**
     * ИНН плательщика
     *
     * @var string
     *
     * @ORM\Column(name="client_inn", type="string", length=12, nullable=false)
     */
    protected $clientInn;

    /**
     * КПП плательщика
     *
     * @var string
     *
     * @ORM\Column(name="client_kpp", type="string", length=9, nullable=false)
     */
    protected $clientKpp;

    /**
     * Юридический адрес плательщика
     *
     * @var string
     *
     * @ORM\Column(name="client_address", type="string", length=300, nullable=false)
     */
    protected $clientAddress;

    /**
     * Invoice constructor.
     */
    public function __construct()
    {
        $this->setCreatedDate(new \DateTime());
        $this->setStatus(self::STATUS_NOT_PAID);
        $this->payments = new ArrayCollection();
    }

    /**
     * Формирует и возвращает номер счета
     *
     * @return string
     */
    public function getNumber()
    {
        $id = $this->getId();
        $code = $this->getType() === self::TYPE_TARIFF ? 'БЛ' : 'БЛС';
        $year = date('y');

        return "{$id}/{$code}/{$year}";
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Invoice
     */
    public function setId(int $id): Invoice
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \Application\Entity\Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }

    /**
     * @param \Application\Entity\Provider $provider
     *
     * @return Invoice
     */
    public function setProvider(Provider $provider): Invoice
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return \Application\Entity\Contract
     */
    public function getContract(): Contract
    {
        return $this->contract;
    }

    /**
     * @param \Application\Entity\Contract $contract
     *
     * @return Invoice
     */
    public function setContract(Contract $contract): Invoice
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return \Application\Entity\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param \Application\Entity\Client $client
     *
     * @return Invoice
     */
    public function setClient(Client $client): Invoice
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Application\Entity\Payment[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param \Application\Entity\Payment $payment
     *
     * @return $this
     */
    public function addPayment(Payment $payment)
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Invoice
     */
    public function setType(string $type): Invoice
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Invoice
     */
    public function setStatus(string $status): Invoice
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->getStatus() === self::STATUS_PAID;
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDate(): \DateTime
    {
        return $this->invoiceDate;
    }

    /**
     * @param \DateTime $invoiceDate
     *
     * @return Invoice
     */
    public function setInvoiceDate(\DateTime $invoiceDate): Invoice
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Invoice
     */
    public function setName(string $name): Invoice
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float
     */
    public function getSum(): float
    {
        return $this->sum;
    }

    /**
     * @param float $sum
     *
     * @return Invoice
     */
    public function setSum(float $sum): Invoice
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return Invoice
     */
    public function setCurrency(string $currency): Invoice
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNds(): ?int
    {
        return $this->nds;
    }

    /**
     * @param int|null $nds
     *
     * @return Invoice
     */
    public function setNds(?int $nds): Invoice
    {
        $this->nds = $nds;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate(): \DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     *
     * @return Invoice
     */
    public function setCreatedDate(\DateTime $createdDate): Invoice
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderFullName(): string
    {
        return $this->providerFullName;
    }

    /**
     * @param string $providerFullName
     *
     * @return Invoice
     */
    public function setProviderFullName(string $providerFullName): Invoice
    {
        $this->providerFullName = $providerFullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderInn(): string
    {
        return $this->providerInn;
    }

    /**
     * @param string $providerInn
     *
     * @return Invoice
     */
    public function setProviderInn(string $providerInn): Invoice
    {
        $this->providerInn = $providerInn;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderKpp(): string
    {
        return $this->providerKpp;
    }

    /**
     * @param string $providerKpp
     *
     * @return Invoice
     */
    public function setProviderKpp(string $providerKpp): Invoice
    {
        $this->providerKpp = $providerKpp;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientFullName(): string
    {
        return $this->clientFullName;
    }

    /**
     * @param string $clientFullName
     *
     * @return Invoice
     */
    public function setClientFullName(string $clientFullName): Invoice
    {
        $this->clientFullName = $clientFullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientInn(): string
    {
        return $this->clientInn;
    }

    /**
     * @param string $clientInn
     *
     * @return Invoice
     */
    public function setClientInn(string $clientInn): Invoice
    {
        $this->clientInn = $clientInn;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientKpp(): string
    {
        return $this->clientKpp;
    }

    /**
     * @param string $clientKpp
     *
     * @return Invoice
     */
    public function setClientKpp(string $clientKpp): Invoice
    {
        $this->clientKpp = $clientKpp;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientAddress(): string
    {
        return $this->clientAddress;
    }

    /**
     * @param string $clientAddress
     *
     * @return Invoice
     */
    public function setClientAddress(string $clientAddress): Invoice
    {
        $this->clientAddress = $clientAddress;

        return $this;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $data
     *
     * @return void
     */
    public function exchangeArray(array $data)
    {
        if (!empty($data['providerFullName'])) {
            $this->setProviderFullName((string)$data['providerFullName']);
        }

        if (!empty($data['providerInn'])) {
            $this->setProviderInn((string)$data['providerInn']);
        }

        if (!empty($data['providerKpp'])) {
            $this->setProviderKpp((string)$data['providerKpp']);
        }

        if (!empty($data['clientFullName'])) {
            $this->setClientFullName((string)$data['clientFullName']);
        }

        if (!empty($data['clientInn'])) {
            $this->setClientInn((string)$data['clientInn']);
        }

        if (!empty($data['clientKpp'])) {
            $this->setClientKpp((string)$data['clientKpp']);
        }

        if (!empty($data['clientAddress'])) {
            $this->setClientAddress((string)$data['clientAddress']);
        }

        if (!empty($data['type'])) {
            $this->setType((string)$data['type']);
        }

        if (!empty($data['status'])) {
            $this->setStatus((string)$data['status']);
        }

        if (!empty($data['name'])) {
            $this->setName((string)$data['name']);
        }

        if (!empty($data['sum'])) {
            $this->setSum(floatval($data['sum']));
        }

        if (!empty($data['currency'])) {
            $this->setCurrency((string)$data['currency']);
        }

        if (!empty($data['nds'])) {
            $this->setNds(intval($data['nds']));
        }

        if (!empty($data['invoiceDate'])) {
            if (!$data['invoiceDate'] instanceof \DateTime) {
                $data['invoiceDate'] = new \DateTime($data['invoiceDate']);
            }
            $this->setInvoiceDate($data['invoiceDate']);
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $data = [
            'id'               => $this->getId(),
            'contract_id'      => $this->getContract()->getId(),
            'provider_id'      => $this->getProvider()->getId(),
            'providerFullName' => $this->getProviderFullName(),
            'providerInn'      => $this->getProviderInn(),
            'providerKpp'      => $this->getProviderKpp(),
            'client_id'        => $this->getClient()->getId(),
            'clientFullName'   => $this->getClientFullName(),
            'clientInn'        => $this->getClientInn(),
            'clientKpp'        => $this->getClientKpp(),
            'clientAddress'    => $this->getClientAddress(),
            'type'             => $this->getType(),
            'status'           => $this->getStatus(),
            'invoiceDate'      => $this->getInvoiceDate()->format('Y-m-d'),
            'name'             => $this->getName(),
            'sum'              => $this->getSum(),
            'currency'         => $this->getCurrency(),
            'nds'              => $this->getNds(),
        ];

        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
