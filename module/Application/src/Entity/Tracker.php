<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfbUser\Entity\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Контролирующая организация
 *
 * @ORM\Entity(repositoryClass="Application\Repository\TrackerRepository")
 * @ORM\Table(name="trackers")
 */
class Tracker
{
    /**
     * Идентификатор контроллирующей организации
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
     * Список Подконтрольных организаций (сервис-провайдеров)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\Entity\Provider")
     * @ORM\JoinTable(name="tracker_providers",
     *      joinColumns={@ORM\JoinColumn(name="tracker_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="provider_id", referencedColumnName="id")}
     * )
     */
    protected $trackingProviders;

    /**
     * Tracker constructor.
     */
    public function __construct()
    {
        $this->trackingProviders = new ArrayCollection();
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
     * @return Tracker
     */
    public function setId(int $id): Tracker
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
     * @return Tracker
     */
    public function setUser(UserInterface $user): Tracker
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTrackingProviders()
    {
        return $this->trackingProviders;
    }

    /**
     * @param \Application\Entity\Provider $provider
     *
     * @return \Application\Entity\Tracker
     */
    public function addTrackingProvider(Provider $provider): Tracker
    {
        if (!$this->trackingProviders->contains($provider)) {
            $this->trackingProviders->add($provider);
        }

        return $this;
    }
}
