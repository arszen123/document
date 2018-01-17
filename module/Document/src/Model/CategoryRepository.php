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
use Document\Entity\File;
use Document\Entity\User;
use Document\Entity\Permission;

/**
 * TODO delete categories if the user has upload permission only else stop delete current tree
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
            if($category->getParent() == null)
                $result .= ',"type":"root"';
            if(!$category->getChildren()->isEmpty())
                $result .= ',"children":' . $this->buildJstree($category->getChildren());
            $result .= '}';
            $i = 1;
        }
        return $result .']';
    }

    public function createCategory(User $user,array $data){
        $entityManager = $this->getEntityManager();
        $parentCategory = $entityManager->find(Category::class,(int) $data['id']);
        $response = new ResponseData();

        if($user == null || $entityManager->getRepository(Permission::class)->canNotCreateCategory($parentCategory,$user)){
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

    private function getUploadPermissionForRoot(Category $category){
        if($category->getParent() != null)
            return $this->getUploadPermissionForRoot($category->getParent());
        return  $category->getPermission()->getUpload();
    }

    public function getRootCategory($category){
        if($category->getParent() != null)
            return $this->getRootCategory($category->getParent());
        return  $category;
    }

    public function editCategory(User $user,array $data){
        $entityManager = $this->getEntityManager();
        $category = $entityManager->find(Category::class,(int) $data['id']);
        $responseData = new ResponseData();
        if(!$entityManager->getRepository(Permission::class)->hasEditPermission($category,$user)) {
            $responseData->setFailMessage('No upload permission!');
            return $responseData;
        }

        $category->setName($data['name']);
        $entityManager->flush();

        $responseData->setSuccessMessage('Successfully edited!');
        return $responseData;
    }

    public function deleteCategory(User $user, $id){
        $this->entityManager = $this->getEntityManager();
        $this->user = $user;
        $responseData = new ResponseData();
        $category = $this->entityManager->getRepository(Category::class)
            ->findOneBy(array('user'=>$user,'id'=>$id));

        if($category == null) {
            $responseData->setFailMessage('Category not found!');
            return $responseData;
        }
        $remove = 1;
        if($category->getPermission()->getUpload() && !$category->getChildren()->isEmpty())
            $remove = $this->deleteCategoriesRecursivly($category->getChildren());
        if($remove == 1) {
            if($this->removeCategory($category))
                $responseData->setSuccessMessage('Deleted successfully!');
            else
                $remove = 0;
        }
        if($remove == 0){
            $responseData->setFailMessage('Some categories may not been deleted!');
        }
        $this->entityManager->flush();
        return $responseData;
    }

    private function deleteCategoriesRecursivly($categories){
        $deleteParent = 1;
        foreach ($categories as $category){
            $deleteCategory = 1;
            if($category->getPermission()->getUpload()) {
                if (!$category->getChildren()->isEmpty())
                    $deleteCategory = $this->deleteCategoriesRecursivly($category->getChildren());
            }else{
                $deleteCategory = 0;
            }

            if($deleteCategory == 1) {
                if ($this->removeCategory($category) == 0)
                    $deleteParent = 0;
            }
            else
                $deleteParent = 0;
        }
        return $deleteParent;
    }

    private function removeCategory($category){
        if($this->user == $category->getUser() && $category->getPermission()->getUpload()) {
            foreach ($category->getFiles() as $file) {
                foreach ($file->getVersions() as $version) {
                    $fileName = $this->entityManager->getRepository(File::class)->getFileSavedName($file,$version);
                    unlink(__DIR__.'/../assets/files/'.$fileName);
                    $this->entityManager->remove($version);
                }
                $this->entityManager->remove($file);
            }
            $this->entityManager->remove($category->getPermission());
            $this->entityManager->remove($category);
            return 1;
        }
        return 0;
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