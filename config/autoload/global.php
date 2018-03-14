<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'navigation' => [
        'default' => [
            [
                'label' => 'Провайдеры',
                'route' => 'providers',
                'class' => 'nav-item', // applied to <li> element
            ],
            [
                'label' => 'Контролирующие организации',
                'route' => 'trackers',
                'class' => 'nav-item',
            ],
            [
                'label' => 'Пользователи',
                'route' => 'zfbuser',
                'class' => 'nav-item',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation'         => \Zend\Navigation\Service\DefaultNavigationFactory::class,
            'abstract_factories' => [
                \Zend\Navigation\Service\NavigationAbstractServiceFactory::class,
            ],
        ],
    ],
    'doctrine'        => [
        // настройка миграций
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/DoctrineORMModule/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
            ],
        ],
    ],
    // Cache configuration.
    'caches'          => [
        'FilesystemCache' => [
            'adapter' => [
                'name'    => \Zend\Cache\Storage\Adapter\Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => './data/cache',
                    // Store cached data for 1 hour.
                    'ttl'       => 60 * 60 * 1,
                ],
            ],
            'plugins' => [
                [
                    'name'    => 'serializer',
                    'options' => [
                    ],
                ],
            ],
        ],
    ],
];
