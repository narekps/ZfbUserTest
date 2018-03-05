<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Сервис-провайдер
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ProviderRepository")
 * @ORM\Table(name="providers")
 */
class Provider extends Contragent
{
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
     * @return string
     */
    public function getEtpContractNumber(): string
    {
        return $this->etpContractNumber;
    }

    /**
     * @param string $etpContractNumber
     *
     * @return Provider
     */
    public function setEtpContractNumber(string $etpContractNumber): Provider
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
     * @return Provider
     */
    public function setEtpContractDate(\DateTime $etpContractDate): Provider
    {
        $this->etpContractDate = $etpContractDate;

        return $this;
    }
}
