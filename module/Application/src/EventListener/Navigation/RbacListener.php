<?php

namespace Application\EventListener\Navigation;

use Zend\EventManager\EventInterface;
use Zend\Navigation\Page\AbstractPage;
use ZfcRbac\Service\AuthorizationServiceInterface;

/**
 * Class RbacListener
 *
 * @package Application\EventListener\Navigation
 */
class RbacListener
{
    /**
     * @var AuthorizationServiceInterface
     */
    protected $authorizationService;

    /**
     * RbacListener constructor.
     *
     * @param \ZfcRbac\Service\AuthorizationServiceInterface $authorizationService
     */
    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @param \Zend\EventManager\EventInterface $event
     *
     * @return bool
     */
    public function accept(EventInterface $event): bool
    {
        $page = $event->getParam('page');
        if (!$page instanceof AbstractPage) {
            return false;
        }

        $permission = $page->getPermission();

        if ($permission === null) {
            return true;
        }

        $event->stopPropagation();

        return $this->authorizationService->isGranted($permission);
    }
}
