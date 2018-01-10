<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 9:43 AM
 */

use Zend\ServiceManager\Factory\InvokableFactory;
use \Zend\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            \Document\Controller\CategoryController::class => InvokableFactory::class,
            \Document\Controller\FileController::class => InvokableFactory::class,
            \Document\Controller\DocumentController::class => InvokableFactory::class
        ]
    ],

        // The following section is new and should be added to your file:
        'router' => [
        'routes' => [
            'category' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/document/category[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => \Document\Controller\CategoryController::class,
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
                        'controller' => \Document\Controller\FileController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'dovument' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/document',
                    'defaults' => [
                        'controller' => \Document\Controller\DocumentController::class,
                        'action' => 'index'
                    ]
                ]
            ]
        ]
    ],

        'view_manager' => [
        'template_path_stack' => [
            'category' => __DIR__ . '/../view',
            'file' => __DIR__.'/../view'
        ],
    ]
];