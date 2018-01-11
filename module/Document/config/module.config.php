<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 9:43 AM
 */

namespace Document;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Document\Controller\CategoryController;
use Document\Controller\DocumentController;
use Document\Controller\FileController;
return [
    'controllers' => [
        'factories' => [
            DocumentController::class => InvokableFactory::class
        ]
    ],
        'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => DocumentController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'document' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/document',
                    'defaults' => [
                        'controller' => DocumentController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'category' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/document/category[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => CategoryController::class,
                        'action'     => 'index'
                    ]
                ]
            ],
            'file' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/document/file[/:id]',
                    'constraints'=>[
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => FileController::class,
                        'action' => 'index'
                    ]
                ]
            ]
        ]
    ],

        'view_manager' => [
            'display_not_found_reason' => true,
            'display_exceptions'       => true,
            'doctype'                  => 'HTML5',
            'not_found_template'       => 'error/404',
            'exception_template'       => 'error/index',
            'template_map' => [
                'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
                'error/404'               => __DIR__ . '/../view/error/404.phtml',
                'error/index'             => __DIR__ . '/../view/error/index.phtml',
            ],
            'template_path_stack' => [
                'category' => __DIR__ . '/../view',
                'file' => __DIR__.'/../view',
                'document' => __DIR__.'/../view'
            ],
    ],

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];