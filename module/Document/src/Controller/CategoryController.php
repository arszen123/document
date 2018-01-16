<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 10:50 AM
 */

namespace Document\Controller;


use Doctrine\ORM\EntityManager;
use Document\Entity\Category;
use Document\Entity\Permission;
use Document\Entity\User;
use Document\Form\CreateCategoryForm;
use Document\Form\EditPermissionForm;
use Document\Model\CreateCategory;
use Document\Model\EditPermission;
use Document\Model\ResponseData;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;

class CategoryController extends AbstractActionController
{
    private $entityManager;
    private $user;

    public function __construct(EntityManager $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function indexAction()
    {
        return $this->listAction();
    }

    public function listAction(){

        $response = $this->entityManager->getRepository(Category::class)->getCategoriesAsResponseData($this->user);
        $response->setResponse($this->getResponse());
        return $response->getResponseAsJsonContentType();
    }

    public function createAction(){
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $id = (int) $this->params()->fromRoute('id', 0);
        $responseData = new ResponseData($this->getResponse());

        $category = $this->entityManager->find(Category::class,$id);
        if($this->entityManager->getRepository(Permission::class)->canNotCreateCategory($category,$this->user)){
            $responseData->setFailMessage('No upload permission!');
            return $responseData->getResponseAsJsonContentType();
        }

        $request = $this->getRequest();
        $form = new CreateCategoryForm();
        $form->get('submit')->setValue('Create Category');

        if(!$request->isPost()){
            $viewModel->setVariables(array('form' => $form));
            return $viewModel;
        }

        $cc = new CreateCategory();
        $form->setInputFilter($cc->getInputFilter());
        $form->setData($request->getPost());

        if(!$form->isValid()){
            $viewModel->setVariables(array('form' => $form));
            return $viewModel;
        }

        $data = $form->getData();
        $data['id'] = $id;

        $responseData = $this->entityManager->getRepository(Category::class)->createCategory($this->user,$data);

        $responseData->setResponse($this->getResponse());
        return $responseData->getResponseAsJsonContentType();
    }

    public function editAction(){
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $id = (int) $this->params()->fromRoute('id', 0);
        $responseData = new ResponseData($this->getResponse());

        $category = $this->entityManager->find(Category::class,$id);
        if(!$this->entityManager->getRepository(Permission::class)->hasEditPermission($category,$this->user)){
            $responseData->setFailMessage('No upload permission!');
            return $responseData->getResponseAsJsonContentType();
        }

        $request = $this->getRequest();
        $form = new CreateCategoryForm();
        $form->get('submit')->setValue('Edit Category');
        $form->get('name')->setValue($category->getName());

        if(!$request->isPost()){
            $viewModel->setVariables(array('form' => $form,'cid'=>$id));
            return $viewModel;
        }

        $cc = new CreateCategory();
        $form->setInputFilter($cc->getInputFilter());
        $form->setData($request->getPost());

        if(!$form->isValid()){
            $viewModel->setVariables(array('form' => $form,'cid'=>$id));
            return $viewModel;
        }

        $data = $form->getData();
        $data['id'] = $id;

        $responseData = $this->entityManager->getRepository(Category::class)->editCategory($this->user,$data);

        $responseData->setResponse($this->getResponse());
        return $responseData->getResponseAsJsonContentType();
    }
    
    public function deleteAction(){
        $id = (int) $this->params()->fromRoute('id', 0);

        $response = $this->entityManager->getRepository(Category::class)->deleteCategory($this->user,$id);
        $response->setResponse($this->getResponse());
        return $response->getResponseAsJsonContentType();
    }

    public function permissionAction(){

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $form = new EditPermissionForm();
        $responseData = new ResponseData($this->getResponse());
        $id = (int) $this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(array('user'=>$this->user,'id'=>$id));

        if($category == null){
            $responseData->setFailMessage('Category not found!');
            return $responseData->getResponseAsJsonContentType();
        }
        $permission = $category->getPermission();
        $form->get('upload')->setValue($permission->getUpload());
        $form->get('download')->setValue($permission->getDownload());

        if(!$request->isPost()){
            return $viewModel->setVariables(['form'=>$form,'cid'=>$id]);
        }

        $ep = new EditPermission();
        $form->setData($request->getPost());
        $form->setInputFilter($ep->getInputFilter());

        if(!$form->isValid()) {
            return $viewModel->setVariables(['form'=>$form,'cid'=>$id]);
        }

        $data = $form->getData();
        $data['id'] = $id;

        $responseData = $this->entityManager->getRepository(Category::class)->changePermissionForCategory($this->user,$data);

        $responseData->setResponse($this->getResponse());
        return $responseData->getResponseAsJsonContentType();
    }
}