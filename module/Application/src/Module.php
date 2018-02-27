<?php

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

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
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setTranslatorLanguage'], 100);
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
            /** @var \Zend\Mvc\I18n\Translator $translator */
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
