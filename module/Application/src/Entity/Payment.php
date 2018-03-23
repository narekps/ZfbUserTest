<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Платеж
 *
 * @ORM\Entity(repositoryClass="Application\Repository\PaymentRepository")
 * @ORM\Table(name="payments")
 */
class Payment implements ArraySerializableInterface, \JsonSerializable
{
    /**
     * Идентификатор платежа
     *
     * @var int
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Application\Entity\Invoice
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Invoice", inversedBy="payments")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false)
     */
    protected $invoice;

    /**
     * Сумма оплаты
     *
     * @var float
     *
     * @ORM\Column(name="sum", type="float", nullable=false)
     */
    protected $sum;

    /**
     * Дата оплаты
     *
     * @var \DateTime
     *
     * @ORM\Column(name="pay_datetime", type="datetime", nullable=false)
     */
    protected $payDateTime;

    /**
     * Номер платежного поручения
     *
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=100, nullable=false)
     */
    protected $number;

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
     * @return Payment
     */
    public function setId(int $id): Payment
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \Application\Entity\Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    /**
     * @param \Application\Entity\Invoice $invoice
     *
     * @return Payment
     */
    public function setInvoice(Invoice $invoice): Payment
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return float
     */
    public function getSum(): float
    {
        return $this->sum;
    }

    /**
     * @param float $sum
     *
     * @return Payment
     */
    public function setSum(float $sum): Payment
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPayDateTime(): \DateTime
    {
        return $this->payDateTime;
    }

    /**
     * @param \DateTime $payDateTime
     *
     * @return Payment
     */
    public function setPayDateTime(\DateTime $payDateTime): Payment
    {
        $this->payDateTime = $payDateTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return Payment
     */
    public function setNumber(string $number): Payment
    {
        $this->number = $number;

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
        if (!empty($data['sum'])) {
            $this->setSum(floatval($data['sum']));
        }

        if (!empty($data['number'])) {
            $this->setNumber($data['number']);
        }

        if (!empty($data['payDateTime'])) {
            if (!$data['payDateTime'] instanceof \DateTime) {
                $data['payDateTime'] = new \DateTime($data['payDateTime']);
            }
            $this->setPayDateTime($data['payDateTime']);
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $data = [
            'id'          => $this->getId(),
            'sum'         => $this->getSum(),
            'number'      => $this->getNumber(),
            'payDateTime' => $this->getPayDateTime()->format('Y-m-d H:i:s'),
            'invoice_id'  => $this->getInvoice()->getId(),
        ];

        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
