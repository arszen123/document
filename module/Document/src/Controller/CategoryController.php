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
use Document\Model\CreateCategory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;

class CategoryController extends AbstractActionController
{
    private $entityManager;
    private $user;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->user = $this->entityManager->find(User::class,1);
    }

    public function indexAction()
    {
        return $this->listAction();
    }

    public function listAction(){

        $json = $this->entityManager->getRepository(Category::class)->getCategoriesAsJstreeJson($this->user);
        $response = $this->getResponse()
            ->setContent($json);
        return $response;
    }

    public function createAction(){
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);


        $request = $this->getRequest();

        $form = new CreateCategoryForm();
        $form->get('submit')->setValue('Create Category');

        if(!$request->isPost()){
            $viewModel->setVariables(array('form' => $form));
            return $viewModel;
            return ['form'=>$form];
        }

        $cc = new CreateCategory();
        $form->setInputFilter($cc->getInputFilter());
        $form->setData($request->getPost());

        if(!$form->isValid()){
            $viewModel->setVariables(array('form' => $form));
            return $viewModel;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        $data = $form->getData();

        if($id != 0)
            $data['id'] = $id;

        $cc->exchangeArray($form->getData());
        $this->entityManager->getRepository(Category::class)->createCategory($this->user,$data);

        $response = $this->getResponse()
            ->setContent('saved');
        return $response;
    }

    public function deleteAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
       if($id != 0)
            $this->entityManager->getRepository(Category::class)->deleteCategory($this->user,$id);
        $response = $this->getResponse()
            ->setContent('deleted');
        return $response;

    }

}