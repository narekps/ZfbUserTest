<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Контролирующая организация
 *
 * @ORM\Entity(repositoryClass="Application\Repository\TrackerRepository")
 * @ORM\Table(name="trackers")
 */
class Tracker extends Contragent
{
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
