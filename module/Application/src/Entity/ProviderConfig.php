<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Настройки Сервис-провайдера
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ProviderConfigRepository")
 * @ORM\Table(name="provider_configs")
 */
class ProviderConfig implements ArraySerializableInterface, \JsonSerializable
{
    /**
     * Провайдер
     *
     * @var Provider
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\OneToOne(targetEntity="Application\Entity\Provider", inversedBy="config")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    protected $provider;

    /**
     * Идентификатор
     *
     * @var string
     *
     * @ORM\Column(name="identifier", type="guid", nullable=false)
     */
    protected $identifier;

    /**
     * Приватный ключ
     *
     * @var string
     *
     * @ORM\Column(name="private_key", type="string", length=100, nullable=false, options={"fixed": true})
     */
    protected $privateKey;

    /**
     * URL для отсылки платежей
     *
     * @var string
     *
     * @ORM\Column(name="payments_url", type="string", length=100, nullable=true)
     */
    protected $paymentsUrl;

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
     * @return ProviderConfig
     */
    public function setProvider(Provider $provider): ProviderConfig
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return ProviderConfig
     */
    public function setIdentifier(string $identifier): ProviderConfig
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     *
     * @return ProviderConfig
     */
    public function setPrivateKey(string $privateKey): ProviderConfig
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPaymentsUrl(): ?string
    {
        return $this->paymentsUrl;
    }

    /**
     * @param null|string $paymentsUrl
     *
     * @return \Application\Entity\ProviderConfig
     */
    public function setPaymentsUrl(?string $paymentsUrl = null): ProviderConfig
    {
        $this->paymentsUrl = $paymentsUrl;

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
        $this->setIdentifier((string)$data['identifier']);
        $this->setPrivateKey((string)$data['privateKey']);
        $this->setPaymentsUrl($data['paymentsUrl']);
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $data = [
            'provider_id' => $this->getProvider()->getId(),
            'identifier'  => $this->getIdentifier(),
            'privateKey'  => $this->getPrivateKey(),
            'paymentsUrl' => $this->getPaymentsUrl(),
        ];

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
