<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfbUser\Entity\User as ZfbUser;

/**
 * @ORM\Entity(repositoryClass="ZfbUser\Repository\UserRepository")
 * @ORM\Table(name="zfb_users")
 */
class User extends ZfbUser
{
    /**
     * Фамилия
     *
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=50, nullable=false)
     */
    protected $surname;

    /**
     * Имя
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    protected $name;

    /**
     * Отчество
     *
     * @var string|null
     *
     * @ORM\Column(name="patronymic", type="string", length=50, nullable=true)
     */
    protected $patronymic;

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        $name = $this->getSurname() . ' ' . $this->getName();

        if (!empty($this->getPatronymic())) {
            $name .= ' ' . $this->getPatronymic();
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     *
     * @return User
     */
    public function setSurname(string $surname): User
    {
        $this->surname = $surname;

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
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    /**
     * @param null|string $patronymic
     *
     * @return User
     */
    public function setPatronymic(?string $patronymic): User
    {
        $this->patronymic = $patronymic;

        return $this;
    }
}
