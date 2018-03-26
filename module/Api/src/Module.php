<?php

namespace Api;

use Zend\Http\PhpEnvironment\Request;
use Zend\EventManager\LazyListenerAggregate;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfbUser\Service\Event\AddUserEvent;
use Zend\Validator\AbstractValidator;
use Application\EventListener\UserService\AddUserEventListener;
use ZfbUser\Service\UserService;
use Application\View\Helper\Navigation as NavigationHelper;
use ZfcRbac\View\Strategy;
use Application\EventListener\Navigation\RbacListener;
use Zend\View\Helper\Navigation\AbstractHelper;

/**
 * Class Module
 *
 * @package Api
 */
class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
