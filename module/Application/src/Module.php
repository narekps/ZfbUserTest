<?php

namespace Application;

use Zend\EventManager\LazyListenerAggregate;
use Zend\Mvc\MvcEvent;
use ZfbUser\Service\Event\AddUserEvent;
use Zend\Validator\AbstractValidator;
use Application\EventListener\UserService\AddUserEventListener;
use ZfbUser\Service\UserService;

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
    public const DEFAULT_LOCALE = 'en_US';

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
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setTranslatorLanguage'], 1);

        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) use ($app) {
            if ($e->getRouteMatch()->getMatchedRouteName() === 'zfbuser/new-user') {
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
        $locale = self::DEFAULT_LOCALE;
        if ($request->getCookie() !== false) {
            $locale = $request->getCookie()->getArrayCopy()['locale'] ?? null;
            if (empty($locale)) {
                $locale = self::DEFAULT_LOCALE;
            }

            $sm = $e->getApplication()->getServiceManager();
            /** @var \Zend\Mvc\I18n\Translator|\Zend\I18n\Translator\Translator $translator */
            $translator = $sm->get('MvcTranslator');
            $translator->setLocale($locale);
        }

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
}
