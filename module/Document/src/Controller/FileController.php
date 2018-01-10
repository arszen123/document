<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 11:01 AM
 */

namespace Document\Controller;




use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\View\Model\JsonModel;

class FileController extends AbstractRestfulController
{

    public function indexAction(){

    }

    public function getList()
    {
        /*$response = $this->getResponseWithHeader()
            ->setContent( __METHOD__.' get the list of data');
        return $response;*/
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