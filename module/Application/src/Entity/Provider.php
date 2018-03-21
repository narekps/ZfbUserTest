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
     * Телефон
     *
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=false)
     */
    protected $phone;

    /**
     * E-mail организации
     *
     * @var string
     * @ORM\Column(name="email", type="string", unique=true, length=50, nullable=false)
     */
    protected $email;

    /**
     * Контактное лицо
     *
     * @var string
     *
     * @ORM\Column(name="contact_person", type="string", length=100, nullable=false)
     */
    protected $contactPerson;

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
     * Exchange internal values from provided array
     *
     * @param  array $data
     *
     * @return void
     */
    public function exchangeArray(array $data)
    {
        parent::exchangeArray($data);

        if (!empty($data['phone'])) {
            $this->setPhone((string)$data['phone']);
        }

        if (!empty($data['contactPerson'])) {
            $this->setContactPerson((string)$data['contactPerson']);
        }

        if (!empty($data['email'])) {
            $this->setEmail((string)$data['email']);
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $data = array_merge(parent::getArrayCopy(), [
            'phone'         => $this->getPhone(),
            'email'         => $this->getEmail(),
            'contactPerson' => $this->getContactPerson(),
        ]);

        return $data;
    }
}
