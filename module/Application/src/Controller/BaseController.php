<?php

namespace Application\Controller;

use ZfbUser\Controller\Plugin;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\User as UserEntity;

/**
 * Class BaseController
 *
 * @method Plugin\ZfbAuthentication zfbAuthentication()
 *
 * @package Application\Controller
 */
class BaseController extends AbstractActionController
{
    /**
     * @return \Application\Entity\User|null
     */
    protected function getCurrentUser(): ?UserEntity
    {
        /** @var UserEntity $identity */
        $identity = $this->zfbAuthentication()->getIdentity();

        return $identity;
    }
}
