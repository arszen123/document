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

/**
 * TODO MANY :D
 * TODO send back error as json
 * TODO delete categories if the user has upload permission only else then stop delete current tree
 */
class CategoryRepository extends EntityRepository
{
    private $user;
    private $entityManager;

    public function getCategoriesAsResponseData(User $user){
        $categories = $this->getCategoriesAsArray($user);
        $response = new ResponseData();
        if($categories == null){
            $response->setFailMessage('No categories available!');
            return $response;
        }

        $response->setSuccessMessage('Categories loaded!');
        $response->setData($this->buildJstree($categories));
        return $response;
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
        $response = new ResponseData();
        if($user == null){
            $response->setFailMessage('You must log in!');
            return $response;
        }

        if($parentCategory != null && !$parentCategory->getPermission()->getUpload()){
            $response->setFailMessage('No upload permission!');
            return $response;
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
                $response->setFailMessage('Wow! You are a wrong way!');
                return $response;
            }
            $category->setParent($parentCategory);
        }

        $entityManager->persist($permission);
        $entityManager->persist($category);
        $entityManager->flush();
        $response->setSuccessMessage('Category created successfully!');
        return $response;
    }

    public function editCategory(User $user,array $data){
        $entityManager = $this->getEntityManager();
        $category = $entityManager->find(Category::class,(int) $data['id']);
        $responseData = new ResponseData();
        if($user == null || $category == null || !$category->getUser() == $user ) {
            $responseData->setFailMessage('Something went wrong!');
            return $responseData;
        }

        if(!$category->getPermission()->getUpload()) {
            $responseData->setFailMessage('No upload permission!');
            return $responseData;
        }

        $category->setName($data['name']);
        $entityManager->flush();

        $responseData->setFailMessage('Successfully edited!');
        return $responseData;
    }

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
        if($this->user == $category->getUser()/* && $category->getPermission()->getUpload()*/) {
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

    public function changePermissionForCategory(User $user,$data){
        $entityManager = $this->getEntityManager();
        $category = $entityManager->find(Category::class,$data['id']);

        $responseData = new ResponseData();
        if(! $category->getUser() == $user){
            $responseData->setFailMessage('Something went wrong!');
            return $responseData;
        }

        $category->getPermission()->setUpload($data['upload']);
        $category->getPermission()->setDownload($data['download']);
        $entityManager->flush();

        $responseData->setSuccessMessage('Successfully edited!');
        return $responseData;
    }
}