<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/11/18
 * Time: 10:21 AM
 */

namespace Document\Model;


use Doctrine\ORM\EntityRepository;
use Document\Entity\Category;
use Document\Entity\User;
use Document\Entity\Permission;


class CategoryRepository extends EntityRepository
{

    public function getCategoriesAsJstreeJson(User $user){
        $categories = $this->getCategoriesAsArray($user);

        return $this->buildJstree($categories);
    }

    public function getCategoriesAsArray(User $user){
        $repo = $this->getEntityManager()->getRepository(Category::class);
        $categories = $repo->findBy(array('user'=>$user,'parent'=>null));
        return $categories;
    }

    private function buildJstree($categories){
        $result = '[';
        $i=0;
        foreach ($categories as $category){
            if($i==1)
                $result .= ',';
            $result .= '{';
            $result .= '"id":"'.$category->getId().'"';
            $result .= ',"text":"'.$category->getName().'"';
            if(!$category->getChildren()->isEmpty()) {
                $result .= ',"children":' . $this->buildJstree($category->getChildren());
            }
            $result .= '}';
            $i = 1;
        }
        return $result .']';
    }

    public function createCategory(User $user,array $data){
        $entityManager = $this->getEntityManager();
        $parentCategory = $entityManager->find(Category::class,(int) $data['id']);

        if($user == null){
            return 1;
        }

        $permission = new Permission();
        $permission->setDownload(true);
        $permission->setUpload(true);

        $category = new Category();
        $category->setName($data['name']);
        $category->setUser($user);
        $category->setPermission($permission);

        if($parentCategory != null){
            if($user != $parentCategory->getUser()){
                return 1;
            }
            $category->setParent($parentCategory);
        }

        $entityManager->persist($permission);
        $entityManager->persist($category);
        $entityManager->flush();
        return 0;
    }

    private $user;
    private $entityManager;
    public function deleteCategory(User $user, $id){
        $this->entityManager = $this->getEntityManager();
        $this->user = $user;

        $category = $this->entityManager->find(Category::class,(int) $id);
        if(!$category->getChildren()->isEmpty())
            $this->deleteCategoriesRecursivly($category->getChildren());
        $this->removeCategory($category);
        $this->entityManager->flush();

    }

    private function deleteCategoriesRecursivly($categories){
        foreach ($categories as $category){
            if(!$category->getChildren()->isEmpty()) {
                $this->deleteCategoriesRecursivly($category->getChildren());
            }
            $this->removeCategory($category);
        }
    }

    private function removeCategory($category){
        if($this->user == $category->getUser() && $category->getPermission()->getUpload()) {
            $this->entityManager->remove($category->getPermission());
            foreach ($category->getFiles() as $file) {
                foreach ($file->getVersions() as $version) {
                    $this->entityManager->remove($version);
                }
                $this->entityManager->remove($file);
            }
            $this->entityManager->remove($category);

        }
    }
}