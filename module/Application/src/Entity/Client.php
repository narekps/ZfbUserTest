<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Клиент
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ClientRepository")
 * @ORM\Table(name="clients")
 */
class Client extends Contragent
{
    /**
     * @var Provider
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $provider;

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
     * @return Client
     */
    public function setProvider(Provider $provider): Client
    {
        $this->provider = $provider;

        return $this;
    }
}
