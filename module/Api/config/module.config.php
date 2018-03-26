<?php

namespace Api;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router'          => [
        'routes' => [
            'api' => [
                'type'          => Literal::class,
                'options'       => [
                    'route'    => '/api',
                    'defaults' => [
                        'controller' => \Application\Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'from-provider' => [
                        'type'    => Segment::class,
                        'options' => [
                            'verb'     => 'GET',
                            'route'    => '/from-provider/:identifier/:jwt',
                            'defaults' => [
                                'controller' => Controller\FromProviderController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\FromProviderController::class => Controller\Factory\FromProviderControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\FromProviderService::class => Service\Factory\FromProviderServiceFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
