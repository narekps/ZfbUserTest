<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfbUser\Entity\UserInterface;

/**
 * Сервис-провайдер
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ProviderRepository")
 * @ORM\Table(name="providers")
 */
class Provider
{
    /**
     * Идентификатор сервис-провайдера
     *
     * @var int
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \ZfbUser\Entity\UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Полное наименование организации
     *
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=1024, nullable=false)
     */
    protected $fullName;

    /**
     * ИНН
     *
     * @var string
     *
     * @ORM\Column(name="inn", type="string", length=12, nullable=false)
     */
    protected $inn;

    /**
     * КПП
     *
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=9, nullable=false)
     */
    protected $kpp;

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
     * @return Provider
     */
    public function setId(int $id): Provider
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \ZfbUser\Entity\UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param \ZfbUser\Entity\UserInterface $user
     *
     * @return Provider
     */
    public function setUser(UserInterface $user): Provider
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     *
     * @return Provider
     */
    public function setFullName(string $fullName): Provider
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getInn(): string
    {
        return $this->inn;
    }

    /**
     * @param string $inn
     *
     * @return Provider
     */
    public function setInn(string $inn): Provider
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * @return string
     */
    public function getKpp(): string
    {
        return $this->kpp;
    }

    /**
     * @param string $kpp
     *
     * @return Provider
     */
    public function setKpp(string $kpp): Provider
    {
        $this->kpp = $kpp;

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
