<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 10:52 AM
 */

namespace Document;


use Doctrine\ORM\EntityManager;
use Document\Entity\User;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

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
                'User' => function($container){
                $em = $container->get('doctrine.entitymanager.orm_default');
                return $em->find(User::class,1);
                }]
            ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\FileController::class => function($container) {
                    $em = $container->get('doctrine.entitymanager.orm_default');
                    $user = $container->get('User');
                    return new Controller\FileController(
                        $em,$user
                    );
                },
                Controller\CategoryController::class => function($container) {
                    $em = $container->get('doctrine.entitymanager.orm_default');
                    $user = $container->get('User');
                    return new Controller\CategoryController(
                        $em,$user
                    );
                }
            ],
        ];
    }
}