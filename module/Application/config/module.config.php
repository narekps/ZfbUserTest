<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

$localCfg = include __DIR__ . '/../../../config/autoload/local.php';

return [
    \ZfbUser\Module::CONFIG_KEY => [
        'module_options' => [
            //required, используется при формировании ссылки для подтверждения аккаунта
            'base_url'           => 'http://zfbuser.dev/',

            // required, see \ZfbUser\Entity\UserInterface
            'user_entity_class'  => Entity\User::class,

            // required, see \ZfbUser\Entity\TokenInterface
            'token_entity_class' => Entity\Token::class,

            // required, соль для хеширования паролей
            'crypt_salt'         => 'SDAFHUKI*^&%$WE$%^Y&UGBFVCSWQE#T',
        ],
        'mail_sender'    => [
            'from_email' => 'noreply@narek.pro',
            'from_name'  => 'ADS',
        ],
        // required if enabled, see https://developers.google.com/recaptcha/docs/display
        'recaptcha'      => $localCfg['google_recaptcha'],
    ],
    'doctrine'                  => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default'             => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
    'router'                    => [
        'routes' => [
            'home'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'providers'   => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/providers[/:action]',
                    'defaults' => [
                        'controller' => Controller\ProvidersController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers'               => [
        'factories' => [
            Controller\IndexController::class     => InvokableFactory::class,
            Controller\ProvidersController::class => Controller\Factory\ProvidersControllerFactory::class,
        ],
    ],
    'service_manager'           => [
        'factories' => [
            EventListener\UserService\AddUserEventListener::class => EventListener\UserService\Factory\AddUserEventListenerFactory::class,
            Form\NewProviderForm::class                           => Form\Factory\NewProviderFormFactory::class,
            Form\NewTrackerForm::class                            => Form\Factory\NewTrackerFormFactory::class,

            //zfbuser services
            'zfbuser_new_user_form'                               => Form\Factory\NewUserFormFactory::class,
        ],
    ],
    'view_manager'              => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];
