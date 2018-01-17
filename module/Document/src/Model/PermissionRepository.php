<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/16/18
 * Time: 2:02 PM
 */

namespace Document\Model;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Document\Entity\Category;
use Document\Entity\User;

class PermissionRepository extends EntityRepository
{
    public function canNotCreateCategory( $category,$user){
        return !$this->canCreateCategory($category,$user);
    }
    public function canCreateCategory( $category,$user){
        if($category==null )
            return true;
        $rootCategory = $this->getEntityManager()->getRepository(Category::class)->getRootCategory($category);
        return $this->hasPermission($category,$user) || $this->hasPermission($rootCategory,$user);
    }

    public function hasPermission($category,$user){
        return ($category->getUser() == $user && $category->getPermission()->getUpload());
    }

    public function hasDownloadPermission($category,$user){
        return ($category->getUser() == $user && $category->getPermission()->getDownload());
    }

    public function hasEditPermission($category,$user){
        return $category != null && $this->hasPermission($category,$user);
    }
}