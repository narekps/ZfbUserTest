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

    //TODO: Добавить остальные поля

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
}
