<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Базовый класс контрагента
 */
abstract class Contragent
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
}

