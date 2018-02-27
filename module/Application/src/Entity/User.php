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
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getIdentity();
    }
}
