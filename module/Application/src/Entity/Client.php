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

}
