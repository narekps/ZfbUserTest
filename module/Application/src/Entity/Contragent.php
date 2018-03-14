<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Базовый класс контрагента
 */
abstract class Contragent implements \JsonSerializable
{
    /**
     * Идентификатор контрагента
     *
     * @var int
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * Юридический адрес
     *
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=300, nullable=false)
     */
    protected $address;

    /**
     * Дата регистрации в системе
     *
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    protected $dateCreated;

    /**
     * Телефон
     *
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=false)
     */
    protected $phone;

    /**
     * Контактное лицо
     *
     * @var string
     *
     * @ORM\Column(name="contact_person", type="string", length=100, nullable=false)
     */
    protected $contactPerson;

    /**
     * E-mail организации
     *
     * @var string
     * @ORM\Column(name="email", type="string", unique=true, length=50, nullable=false)
     */
    protected $email;

    /**
     * Contragent constructor.
     */
    public function __construct()
    {
        $this->dateCreated = new \DateTime();
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
     * @return Contragent
     */
    public function setId(int $id): Contragent
    {
        $this->id = $id;

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
     * @return Contragent
     */
    public function setFullName(string $fullName): Contragent
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
     * @return Contragent
     */
    public function setInn(string $inn): Contragent
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
     * @return Contragent
     */
    public function setKpp(string $kpp): Contragent
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Contragent
     */
    public function setAddress(string $address): Contragent
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return Contragent
     */
    public function setDateCreated(\DateTime $dateCreated): Contragent
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Contragent
     */
    public function setPhone(string $phone): Contragent
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactPerson(): string
    {
        return $this->contactPerson;
    }

    /**
     * @param string $contactPerson
     *
     * @return Contragent
     */
    public function setContactPerson(string $contactPerson): Contragent
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Contragent
     */
    public function setEmail(string $email): Contragent
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'fullName'      => $this->getFullName(),
            'inn'           => $this->getInn(),
            'kpp'           => $this->getKpp(),
            'dateCreated'   => $this->getDateCreated()->format('Y-m-d'),
            'phone'         => $this->getPhone(),
            'address'       => $this->getAddress(),
            'contactPerson' => $this->getContactPerson(),
            'email'         => $this->getEmail(),
        ];

        return $data;
    }
}
