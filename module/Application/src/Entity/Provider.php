<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Сервис-провайдер
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ProviderRepository")
 * @ORM\Table(name="providers")
 */
class Provider extends Contragent
{
}
