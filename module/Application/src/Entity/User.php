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
     * @var \Application\Entity\Tracker
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Tracker")
     * @ORM\JoinColumn(name="tracker_id", referencedColumnName="id", nullable=true)
     */
    protected $tracker;

    /**
     * @var \Application\Entity\Provider
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", nullable=true)
     */
    protected $provider;

    /**
     * @var \Application\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    protected $client;

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
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
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
     * @return \Application\Entity\Tracker
     */
    public function getTracker(): Tracker
    {
        return $this->tracker;
    }

    /**
     * @param \Application\Entity\Tracker $tracker
     *
     * @return User
     */
    public function setTracker(Tracker $tracker): User
    {
        $this->tracker = $tracker;

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
     * @return User
     */
    public function setProvider(Provider $provider): User
    {
        $this->provider = $provider;

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
     * @return User
     */
    public function setClient(Client $client): User
    {
        $this->client = $client;

        return $this;
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
