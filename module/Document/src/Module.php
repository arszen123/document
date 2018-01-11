<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 10:52 AM
 */

namespace Document;


use Doctrine\ORM\EntityManager;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'EntityManagerFactory' => function($container) {
                    $em = $container->get('doctrine.entitymanager.orm_default');
                    return new $em;
                }]
            ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\FileController::class => function($container) {
                    return new Controller\FileController(
                        $container->get('doctrine.entitymanager.orm_default')
                    );
                },
                Controller\CategoryController::class => function($container) {
                    return new Controller\CategoryController(
                        $container->get('doctrine.entitymanager.orm_default')
                    );
                }
            ],
        ];
    }
}