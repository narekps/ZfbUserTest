<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfbUser\Entity\User as ZfbUser;
use ZfcRbac\Identity\IdentityInterface;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\UserRepository")
 * @ORM\Table(name="zfb_users")
 */
class User extends ZfbUser implements IdentityInterface
{
    /**
     * @var \Application\Entity\Tracker|null
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Tracker")
     * @ORM\JoinColumn(name="tracker_id", referencedColumnName="id", nullable=true)
     */
    protected $tracker;

    /**
     * @var \Application\Entity\Provider|null
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", nullable=true)
     */
    protected $provider;

    /**
     * @var \Application\Entity\Client|null
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
     * Роль
     *
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=50, nullable=false)
     */
    protected $role;

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
     * @return \Application\Entity\Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param \Application\Entity\Client|null $client
     *
     * @return User
     */
    public function setClient(?Client $client): User
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Application\Entity\Tracker|null
     */
    public function getTracker(): ?Tracker
    {
        return $this->tracker;
    }

    /**
     * @param \Application\Entity\Tracker|null $tracker
     *
     * @return User
     */
    public function setTracker(?Tracker $tracker): User
    {
        $this->tracker = $tracker;

        return $this;
    }

    /**
     * @return \Application\Entity\Provider|null
     */
    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    /**
     * @param \Application\Entity\Provider|null $provider
     *
     * @return User
     */
    public function setProvider(?Provider $provider): User
    {
        $this->provider = $provider;

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

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function setRole(string $role): User
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return \Application\Entity\Contragent|null
     */
    public function getContragent(): ?Contragent
    {
        if ($this->getProvider()) {
            return $this->getProvider();
        }

        if ($this->getTracker()) {
            return $this->getTracker();
        }

        if ($this->getClient()) {
            return $this->getClient();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getContragentName(): string
    {
        if ($this->getContragent()) {
            return $this->getContragent()->getFullName();
        }

        return '';
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

        $this->setSurname((string)$data['surname']);
        $this->setName((string)$data['name']);
        $this->setPatronymic((string)$data['patronymic']);
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $data = array_merge(parent::getArrayCopy(), [
            'surname'    => $this->getSurname(),
            'name'       => $this->getName(),
            'patronymic' => $this->getPatronymic(),
        ]);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return [$this->getRole()];
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getRole() === 'admin';
    }
}
