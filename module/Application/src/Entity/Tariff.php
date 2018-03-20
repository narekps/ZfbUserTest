<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Тариф сервис-провайдера
 *
 * @ORM\Entity(repositoryClass="Application\Repository\TariffRepository")
 * @ORM\Table(name="tariffs")
 */
class Tariff implements ArraySerializableInterface, \JsonSerializable
{

    const STATUS_NEW = 'new';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ACTIVE = 'active';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ARCHIVE = 'archive';

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_NEW        => 'Новый',
            self::STATUS_PROCESSING => 'В обработке',
            self::STATUS_ACTIVE     => 'Действующий',
            self::STATUS_REJECTED   => 'Отклонен',
            self::STATUS_ARCHIVE    => 'Архивный',
        ];
    }

    /**
     * Идентификатор тарифа
     *
     * @var int
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Application\Entity\Provider
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", nullable=false)
     */
    protected $provider;

    /**
     * @var \Application\Entity\Contract
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Contract")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id", nullable=false)
     */
    protected $contract;

    /**
     * Наименование тарифа
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=500, nullable=false)
     */
    protected $name;

    /**
     * Описание тарифа
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=false)
     */
    protected $description;

    /**
     * Стоимость тарифа
     *
     * @var float
     *
     * @ORM\Column(name="cost", type="float", nullable=false)
     */
    protected $cost;

    /**
     * Ставка НДС
     *
     * @var int|null
     *
     * @ORM\Column(name="nds", type="integer", nullable=true)
     */
    protected $nds;

    /**
     * Дата окончания продаж
     *
     * @var \DateTime
     *
     * @ORM\Column(name="sale_end_date", type="date", nullable=false)
     */
    protected $saleEndDate;

    /**
     * Валюта
     *
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false, options={"fixed": true})
     */
    protected $currency;

    /**
     * Статус
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=false)
     */
    protected $status;

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
     * @return Tariff
     */
    public function setId(int $id): Tariff
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
     * @return Tariff
     */
    public function setProvider(Provider $provider): Tariff
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
     * @return Tariff
     */
    public function setContract(Contract $contract): Tariff
    {
        $this->contract = $contract;

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
     * @return Tariff
     */
    public function setName(string $name): Tariff
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Tariff
     */
    public function setDescription(string $description): Tariff
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     *
     * @return Tariff
     */
    public function setCost(float $cost): Tariff
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return int
     */
    public function getNds(): ?int
    {
        return $this->nds;
    }

    /**
     * @param int $nds
     *
     * @return Tariff
     */
    public function setNds(?int $nds): Tariff
    {
        $this->nds = $nds;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSaleEndDate(): \DateTime
    {
        return $this->saleEndDate;
    }

    /**
     * @param \DateTime $saleEndDate
     *
     * @return Tariff
     */
    public function setSaleEndDate(\DateTime $saleEndDate): Tariff
    {
        $this->saleEndDate = $saleEndDate;

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
     * @return Tariff
     */
    public function setCurrency(string $currency): Tariff
    {
        $this->currency = $currency;

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
     * @return Tariff
     */
    public function setStatus(string $status): Tariff
    {
        $this->status = $status;

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
        if (!empty($data['name'])) {
            $this->setName($data['name']);
        }

        if (!empty($data['description'])) {
            $this->setDescription($data['description']);
        }

        if (!empty($data['cost'])) {
            $this->setCost(floatval($data['cost']));
        }

        if (!empty($data['nds'])) {
            $this->setNds($data['nds']);
        }

        if (!empty($data['saleEndDate'])) {
            if (!$data['saleEndDate'] instanceof \DateTime) {
                $data['saleEndDate'] = new \DateTime($data['saleEndDate']);
            }
            $this->setSaleEndDate($data['saleEndDate']);
        }

        if (!empty($data['currency'])) {
            $this->setCurrency($data['currency']);
        }

        if (!empty($data['status'])) {
            $this->setStatus($data['status']);
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
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'cost'        => $this->getCost(),
            'nds'         => $this->getNds() === null ? -1 : $this->getNds(),
            'saleEndDate' => $this->getSaleEndDate()->format('Y-m-d'),
            'currency'    => $this->getCurrency(),
            'status'      => $this->getStatus(),
            'contract_id' => $this->getContract()->getId(),
            'provider_id' => $this->getProvider()->getId(),
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
