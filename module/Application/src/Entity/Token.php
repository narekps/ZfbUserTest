<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfbUser\Entity\Token as ZfbToken;

/**
 * @ORM\Entity(repositoryClass="ZfbUser\Repository\TokenRepository")
 * @ORM\Table(name="zfb_tokens", indexes={
 *     @ORM\Index(name="user_value_type_idx", columns={"user_id", "value", "type"}),
 *     @ORM\Index(name="revoked_expired_at_user_id_type_idx", columns={"revoked", "expired_at", "user_id", "type"})
 * })
 */
class Token extends ZfbToken
{
    /**
     * @var \ZfbUser\Entity\UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $user;
}
