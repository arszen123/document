<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 11:01 AM
 */

namespace Document\Controller;



use Doctrine\ORM\EntityManager;
use Document\Entity\Category;
use Document\Entity\User;
use Document\Entity\File;
use Document\Entity\Version;
use Document\Form\UploadFileForm;
use Document\Model\FileDetailes;
use Document\Model\ResponseData;
use Document\Model\UploadFile;
use Zend\Filter\File\RenameUpload;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FileController extends AbstractActionController
{

    private $entityManager;
    private $user;

    public function __construct(EntityManager $entityManager,User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function indexAction()
    {
        return $this->listAction();
    }

    public function listAction(){
        $data['user'] = $this->user;
        $data['categoryId'] = $this->params()->fromRoute('id',0);
        $responseData = $this->entityManager->getRepository(File::class)->getFilesAsJson($data);
        $responseData->setResponse($this->getResponse());
        return $responseData->getResponseAsJsonContentType();
    }

    public function uploadAction(){
        $viewModel = new ViewModel();
        $responseData = new ResponseData($this->getResponse());
        $request = $this->getRequest();
        $categoryId = (int) $this->params()->fromRoute('id',0);

        $data['user'] = $this->user;
        $data['categoryId'] = $categoryId;

        $viewModel->setTerminal(true);

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(array('user'=>$this->user,'id'=>$data['categoryId']));
        if($category == null){
            $responseData->setFailMessage('Category not found!');
            return $responseData->getResponseAsJsonContentType();
        }

        if($category->getParent() == null){
            $responseData->setFailMessage('Cannot upload files to root category!');
            return $responseData->getResponseAsJsonContentType();
        }

        if(!$category->getPermission()->getUpload()){
            $responseData->setFailMessage('No upload permission to current category!');
            return $responseData->getResponseAsJsonContentType();
        }

        $form = new UploadFileForm();

        if(!$request->isPost()){
            $viewModel->setVariables(['form'=>$form,'cid'=>$data['categoryId']]);
            return $viewModel;
        }

        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $uf = new UploadFile();
        $form->setData($post);
        $form->setInputFilter($uf->getInputFilter());

        if(!$form->isValid()) {
            $viewModel->setVariables(['form'=>$form,'cid'=>$data['categoryId']]);
            return $viewModel;
        }

        $data = $form->getData();
        $data['categoryId'] = $categoryId;
        $data['user'] = $this->user;
        $newFileName = $this->entityManager->getRepository(File::class)->saveFileInDatabase($data);

        //Upload
        $uf->saveFile($data['file'],$newFileName);
        $responseData->setSuccessMessage('File uploaded!');
        return $responseData->getResponseAsJsonContentType();


    }

    public function downloadAction(){
        $fileId = $this->params()->fromRoute('id',0);
        $versionId = $this->params()->fromRoute('versionId',0);
        $response = $this->getResponse();

        $fileData = $this->entityManager->getRepository(File::class)->getFile($fileId,$versionId,$this->user);
        if($fileData instanceof ResponseData) {
            $fileData->setResponse($response);
            return $fileData->getResponseAsJsonContentType();
        }
        $fileName = __DIR__.'/../assets/files/'.$fileData['versionName'];
        $stream = new Stream();
        $stream->setStream(fopen($fileName, 'r'));
        $stream->setStatusCode(200);
        $stream->setStreamName(basename($fileData['file']->getFilename()));
        $headers = new \Zend\Http\Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . basename($fileData['file']->getFilename()) .'"',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => filesize($fileName),
            'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ));
        $stream->setHeaders($headers);
        return $stream;
    }

    public function detailesAction(){
        $fileId = $this->params()->fromRoute('id',0);
        $versionId = $this->params()->fromRoute('versionId',0);
        $response = $this->getResponse();

        $fileData = $this->entityManager->getRepository(File::class)->getFile($fileId,$versionId,$this->user);
        if($fileData instanceof ResponseData) {
            $fileData->setResponse($response);
            return $fileData->getResponseAsJsonContentType();
        }
        $response = new ResponseData($this->getResponse());
        $data = new FileDetailes($fileData['file'],$fileData['version']);
        $response->setData($data->getDetailesAsJosnObject());
        $response->setSuccessMessage('Detailes loaded!');
        return $response->getResponseAsJsonContentType();
    }
}