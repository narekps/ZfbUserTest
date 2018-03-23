<?php

namespace Application;

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
 * @package Application
 */
class Module
{
    const VERSION = '3.0.3-dev';

    /**
     * Поддерживаемые локали
     */
    public const LOCALES = [
        'en_US' => 'English',
        'ru_RU' => 'Русский',
    ];

    /**
     * Дефолтная локаль
     */
    public const DEFAULT_LOCALE = 'ru_RU';

    /**
     * @param \Zend\Mvc\MvcEvent $e
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onBootstrap(MvcEvent $e)
    {
        AbstractValidator::setDefaultTranslator($e->getApplication()->getServiceManager()->get('MvcTranslator'));

        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $em = $app->getEventManager();
        $sem = $em->getSharedManager();
        $rbacListener = $sm->get(RbacListener::class);
        $sem->attach(AbstractHelper::class, 'isAllowed', [$rbacListener, 'accept']);

        $redirectStrategyListener = $sm->get(Strategy\RedirectStrategy::class);
        $redirectStrategyListener->attach($app->getEventManager());

        $unauthorizedStrategyListener = $sm->get(Strategy\UnauthorizedStrategy::class);
        $unauthorizedStrategyListener->attach($app->getEventManager());

        $this->initNavigationHelpers($app->getServiceManager());

        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setTranslatorLanguage'], 1);
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) use ($app) {
            if ($e->getRouteMatch()->getMatchedRouteName() === 'zfbuser/new-user' || $e->getRouteMatch()->getMatchedRouteName() === 'zfbuser/api/new-user') {
                /** @var \ZfbUser\Service\UserService $userService */
                $userService = $app->getServiceManager()->get(UserService::class);
                $aggregate = new LazyListenerAggregate(
                    [
                        [
                            'listener' => AddUserEventListener::class,
                            'method'   => 'onAddUserPre',
                            'event'    => AddUserEvent::EVENT_PRE,
                            'priority' => 100,
                        ],
                    ],
                    $app->getServiceManager()
                );
                $aggregate->attach($userService->getEventManager());

                return true;
            }

            return true;
        }, 10);
    }

    /**
     * @param \Zend\Mvc\MvcEvent $e
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setTranslatorLanguage(MvcEvent $e)
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $e->getRequest();
        if (!$request instanceof Request || $request->isXmlHttpRequest()) {
            //return;
        }

        $locale = self::DEFAULT_LOCALE;
        if ($request->getCookie() !== false) {
            $locale = $request->getCookie()->getArrayCopy()['locale'] ?? null;
            if (empty($locale)) {
                $locale = self::DEFAULT_LOCALE;
            }
        }

        $sm = $e->getApplication()->getServiceManager();
        /** @var \Zend\Mvc\I18n\Translator|\Zend\I18n\Translator\Translator $translator */
        $translator = $sm->get('MvcTranslator');
        $translator->setLocale($locale);

        $e->getViewModel()->currentLocale = $locale;
        $e->getViewModel()->locales = self::LOCALES;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function initNavigationHelpers(ServiceLocatorInterface $sm)
    {
        /** @var \Zend\View\Helper\Navigation\PluginManager $pm */
        $pm = $sm->get('ViewHelperManager')->get('Navigation')->getPluginManager();
        $pm->setAlias('Navbar', NavigationHelper\Navbar::class);
        $pm->setFactory(NavigationHelper\Navbar::class, InvokableFactory::class);
    }
}
