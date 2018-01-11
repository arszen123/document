<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 11:01 AM
 */

namespace Document\Controller;




//use \Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\View\Model\JsonModel;

class FileController extends AbstractRestfulController
{

    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction(){
        return $this->getList();
    }

    public function getList()
    {

        $userId = 1;

        $user = $this->entityManager->find(\Document\Entity\User::class,$userId);
        if($user == null){
            $response = $this->getResponseWithHeader()
                ->setContent('User not found');
            return $response;
        }
        /*$categoriesRepository = $this->entityManager->getRepository('Category');
        $categories = $categoriesRepository->findBy(array('user'=>$user,'parent'=>null));//findAll();

        $time = new DateTime();
        echo $time->format("Y-m-d H:i:s\n");


       $res = listCategories($categories);

        function listCategories($categories, $format=""){
            $res = '';
            foreach($categories as $category){
                $res .=  sprintf($format."%d - %s - %s - %d,%d\n",$category->getId(), $category->getUser()->getName(), $category->getName(),
                    $category->getPermission()->getUpload(), $category->getPermission()->getDownload());
                $res .= listFiles($category->getFiles(),$format);
                if(!$category->getChildren()->isEmpty()){
                    $res .= listCategories($category->getChildren(),$format."\t");
                }
            }
            return $res;
        }

        function listFiles($files,$format=""){
            $res = '';
            foreach($files as $file){
                $res .= sprintf($format."%d - %s - v%s.0 - %s\n",$file->getId(),$file->getVisibleName(),$file->getVersions()->last()->getVersion(),$file->getVersions()->last()->getUploaded()->format("Y-m-d H:i:s"));
            }
            return $res;
        }*/
        $response = $this->getResponseWithHeader()
            ->setContent($user->getName());
        return $response;
    }

    public function get($id)
    {
        $response = $this->getResponseWithHeader()
            ->setContent("{\"value\":\"".$id."\"}");
        return $response;
    }

    public function create($data)
    {
        $response = $this->getResponseWithHeader()
            ->setContent( __METHOD__.'create new item of data :</br>'.$data['id'].'</b>');
        return $response;
    }

    public function update($id, $data)
    {
        $response = $this->getResponseWithHeader()
            ->setContent(__METHOD__.' update current data with id =  '.$id.
                ' with data of name is '.$data['name']) ;
        return $response;
    }

    public function delete($id)
    {
        $response = $this->getResponseWithHeader()
            ->setContent(__METHOD__.' delete current data with id =  '.$id) ;
        return $response;
    }

    public function options()
    {
        $t = $this->getRequest();
        $response = $this->getResponseWithHeader()
            ->setContent(__METHOD__.' delete current data with id =  1'.explode('=',$t->getContent())[1]);


        return $response;
    }

    // configure response
    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
            //make can accessed by *
            ->addHeaderLine('Access-Control-Allow-Origin','*')
            //set allow methods
            ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET OPTIONS');

        return $response;
    }

}