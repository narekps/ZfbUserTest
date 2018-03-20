<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\ContractRepository")
 * @ORM\Table(name="contracts")
 */
class Contract implements ArraySerializableInterface,\JsonSerializable
{
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
     * @var Provider
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $provider;

    /**
     * Номер договора с ЭТП ГПБ
     *
     * @var string
     *
     * @ORM\Column(name="etp_contract_number", type="string", length=50, nullable=false)
     */
    protected $etpContractNumber;

    /**
     * Дата договора с ЭТП ГПБ
     *
     * @var \DateTime
     *
     * @ORM\Column(name="etp_contract_date", type="date", nullable=false)
     */
    protected $etpContractDate;

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
     * @return Contract
     */
    public function setId(int $id): Contract
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
     * @return Contract
     */
    public function setProvider(Provider $provider): Contract
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string
     */
    public function getEtpContractNumber(): string
    {
        return $this->etpContractNumber;
    }

    /**
     * @param string $etpContractNumber
     *
     * @return \Application\Entity\Contract
     */
    public function setEtpContractNumber(string $etpContractNumber): Contract
    {
        $this->etpContractNumber = $etpContractNumber;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEtpContractDate(): \DateTime
    {
        return $this->etpContractDate;
    }

    /**
     * @param \DateTime $etpContractDate
     *
     * @return \Application\Entity\Contract
     */
    public function setEtpContractDate(\DateTime $etpContractDate): Contract
    {
        $this->etpContractDate = $etpContractDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return '№' . $this->getEtpContractNumber() . ' от ' . $this->getFormattedDate($this->getEtpContractDate());
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    private function getFormattedDate(\DateTime $date)
    {
        $formatter = new \IntlDateFormatter('ru_RU', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
        $formatter->setPattern('dd MMMM YYYY');

        return $formatter->format($date);
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
        if (!empty($data['etpContractNumber'])) {
            $this->setEtpContractNumber((string)$data['etpContractNumber']);
        }

        if (!empty($data['etpContractDate'])) {
            if (!$data['etpContractDate'] instanceof \DateTime) {
                $data['etpContractDate'] = new \DateTime($data['etpContractDate']);
            }
            $this->setEtpContractDate($data['etpContractDate']);
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
            'id'                => $this->getId(),
            'etpContractNumber' => $this->getEtpContractNumber(),
            'etpContractDate'   => $this->getEtpContractDate()->format('Y-m-d'),
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
