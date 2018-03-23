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
            'base_url'           => 'http://zfbuser.local/',

            // required, see \ZfbUser\Entity\UserInterface
            'user_entity_class'  => Entity\User::class,

            // required, see \ZfbUser\Entity\TokenInterface
            'token_entity_class' => Entity\Token::class,

            'authentication_callback_route'   => 'home',

            // required, соль для хеширования паролей
            'crypt_salt'         => 'SDAFHUKI*^&%$WE$%^Y&UGBFVCSWQE#T',
        ],
        'mail_sender'    => [
            'from_email' => 'noreply@narek.pro',
            'from_name'  => 'ADS',
        ],
        // required if enabled, see https://developers.google.com/recaptcha/docs/display
        'recaptcha'      => $localCfg['google_recaptcha'],
        'new_user_form'  => [
            'form_name' => 'contragentForm',
        ],
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
            'home'      => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'providers' => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'        => 'GET',
                    'route'       => '/providers',
                    'defaults'    => [
                        'controller' => Controller\ProvidersController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'info'   => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/info/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ProvidersController::class,
                                'action'     => 'info',
                            ],
                        ],
                    ],
                    'get'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/get/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ProvidersController::class,
                                'action'     => 'get',
                            ],
                        ],
                    ],
                    'update' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'POST',
                            'route'       => '/update/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ProvidersController::class,
                                'action'     => 'update',
                            ],
                        ],
                    ],
                ],
            ],
            'trackers'  => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'        => 'GET',
                    'route'       => '/trackers',
                    'defaults'    => [
                        'controller' => Controller\TrackersController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'info'   => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/info/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TrackersController::class,
                                'action'     => 'info',
                            ],
                        ],
                    ],
                    'get'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/get/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TrackersController::class,
                                'action'     => 'get',
                            ],
                        ],
                    ],
                    'update' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'POST',
                            'route'       => '/update/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TrackersController::class,
                                'action'     => 'update',
                            ],
                        ],
                    ],
                ],
            ],
            'invoices'  => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'        => 'GET',
                    'route'       => '/invoices',
                    'defaults'    => [
                        'controller' => Controller\InvoicesController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'provider' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/provider/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\InvoicesController::class,
                                'action'     => 'provider',
                            ],
                        ],
                    ],
                    'tracker' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/tracker/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\InvoicesController::class,
                                'action'     => 'tracker',
                            ],
                        ],
                    ],
                    'client' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/client/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\InvoicesController::class,
                                'action'     => 'client',
                            ],
                        ],
                    ],
                    'download' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/download/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\InvoicesController::class,
                                'action'     => 'download',
                            ],
                        ],
                    ],
                    'create'   => [
                        'type'    => Literal::class,
                        'options' => [
                            'verb'     => 'POST',
                            'route'    => '/create',
                            'defaults' => [
                                'controller' => Controller\InvoicesController::class,
                                'action'     => 'create',
                            ],
                        ],
                    ],
                ],
            ],
            'tariffs'   => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'        => 'GET',
                    'route'       => '/tariffs',
                    'defaults'    => [
                        'controller' => Controller\TariffsController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'provider'     => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/provider/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'provider',
                            ],
                        ],
                    ],
                    'client'     => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/client/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'client',
                            ],
                        ],
                    ],
                    'get'     => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/get/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'get',
                            ],
                        ],
                    ],
                    'create'  => [
                        'type'    => Literal::class,
                        'options' => [
                            'verb'     => 'POST',
                            'route'    => '/create',
                            'defaults' => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'create',
                            ],
                        ],
                    ],
                    'update'  => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'POST',
                            'route'       => '/update/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'update',
                            ],
                        ],
                    ],
                    'archive' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'POST',
                            'route'       => '/archive/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'archive',
                            ],
                        ],
                    ],
                    'pay'     => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'POST',
                            'route'       => '/pay/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TariffsController::class,
                                'action'     => 'pay',
                            ],
                        ],
                    ],
                ],
            ],
            'reports'   => [
                'type'    => Segment::class,
                'options' => [
                    'verb'        => 'GET',
                    'route'       => '/reports',
                    'defaults'    => [
                        'controller' => Controller\ReportsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'contracts' => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'     => 'GET',
                    'route'    => '/contracts',
                    'defaults' => [
                        'controller' => Controller\ContractsController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'create' => [
                        'type'    => Literal::class,
                        'options' => [
                            'verb'     => 'POST',
                            'route'    => '/create',
                            'defaults' => [
                                'controller' => Controller\ContractsController::class,
                                'action'     => 'create',
                            ],
                        ],
                    ],
                    'update' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'POST',
                            'route'       => '/update/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ContractsController::class,
                                'action'     => 'update',
                            ],
                        ],
                    ],
                ],
            ],
            'clients' => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'        => 'GET',
                    'route'       => '/clients',
                    'defaults'    => [
                        'controller' => Controller\ClientsController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'info'   => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/info/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ClientsController::class,
                                'action'     => 'info',
                            ],
                        ],
                    ],
                ],
            ],
            'users'  => [
                'type'          => Segment::class,
                'options'       => [
                    'verb'        => 'GET',
                    'route'       => '/users',
                    'defaults'    => [
                        'controller' => Controller\UsersController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'provider' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/provider/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\UsersController::class,
                                'action'     => 'provider',
                            ],
                        ],
                    ],
                    'tracker' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'        => 'GET',
                            'route'       => '/tracker/:id',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\UsersController::class,
                                'action'     => 'tracker',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'               => [
        'factories' => [
            Controller\IndexController::class     => InvokableFactory::class,
            Controller\ReportsController::class   => InvokableFactory::class,
            Controller\ProvidersController::class => Controller\Factory\ProvidersControllerFactory::class,
            Controller\TrackersController::class  => Controller\Factory\TrackersControllerFactory::class,
            Controller\ClientsController::class   => Controller\Factory\ClientsControllerFactory::class,
            Controller\TariffsController::class   => Controller\Factory\TariffsControllerFactory::class,
            Controller\ContractsController::class => Controller\Factory\ContractsControllerFactory::class,
            Controller\InvoicesController::class  => Controller\Factory\InvoicesControllerFactory::class,
            Controller\UsersController::class     => Controller\Factory\UsersControllerFactory::class,
        ],
    ],
    'service_manager'           => [
        'factories' => [
            EventListener\UserService\AddUserEventListener::class => EventListener\UserService\Factory\AddUserEventListenerFactory::class,
            EventListener\Navigation\RbacListener::class          => EventListener\Navigation\Factory\RbacListenerFactory::class,

            Form\NewProviderForm::class                           => Form\Factory\NewProviderFormFactory::class,
            Form\EditProviderForm::class                          => Form\Factory\EditProviderFormFactory::class,
            Form\NewTrackerForm::class                            => Form\Factory\NewTrackerFormFactory::class,
            Form\EditTrackerForm::class                           => Form\Factory\EditTrackerFormFactory::class,
            Form\NewUserForm::class                               => Form\Factory\NewUserFormFactory::class,
            Form\UpdateUserForm::class                            => Form\Factory\UpdateUserFormFactory::class,
            Form\TariffForm::class                                => Form\Factory\TariffFormFactory::class,
            Form\ContractForm::class                              => InvokableFactory::class,
            Form\InvoiceForm::class                               => Form\Factory\InvoiceFormFactory::class,
            Service\TariffService::class                          => Service\Factory\TariffServiceFactory::class,
            Service\InvoiceService::class                         => Service\Factory\InvoiceServiceFactory::class,
            Service\ContractService::class                        => Service\Factory\ContractServiceFactory::class,
            Service\ProviderService::class                        => Service\Factory\ProviderServiceFactory::class,
            Service\TrackerService::class                         => Service\Factory\TrackerServiceFactory::class,

            //zfbuser services
            'zfbuser_new_user_form'                               => Form\Factory\UserFormFactory::class,
            'zfbuser_update_user_form'                            => Form\Factory\UpdateUserFormFactory::class,
            'zfbuser_user_repository'                             => Repository\Factory\UserRepositoryFactory::class,
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
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'factories'  => [
            View\Helper\DateFormat::class  => InvokableFactory::class,
        ],
        'aliases'    => [
            'dateFormat' => View\Helper\DateFormat::class,
        ],
    ],

    'translator' => [
        'locale'                    => 'ru_RU',
        'translation_file_patterns' => [
            [
                'type'     => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
        ],
        /*'cache'  => [
            'adapter' => [
                'name'    => 'Filesystem',
                'options' => [
                    'cache_dir' => __DIR__ . '/../../../data/cache',
                    'ttl'       => 3600,
                ],
            ],
            'plugins' => [
                [
                    'name'    => 'serializer',
                    'options' => [],
                ],
                'exception_handler' => [
                    'throw_exceptions' => true,
                ],
            ],
        ],*/
    ],
];
