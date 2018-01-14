<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/13/18
 * Time: 2:35 PM
 */

namespace Document\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Document\Entity\Category;
use Document\Entity\File;
use Document\Entity\User;
use Document\Entity\Version;

class FileRepository extends EntityRepository
{

    /**
     * No external usage, but keep it public
     */
    public function getFilesAsArray(array $data){
        $entityManager = $this->getEntityManager();
        $category = $entityManager->find(Category::class,$data['categoryId']);

        if($category == null || $category->getUser() != $data['user'] ){
            return null;
        }
        if($category->getFiles()->isEmpty())
            return null;
        return $category->getFiles();
    }

    public function getFilesAsJson(array $data){
        $files = $this->getFilesAsArray($data);
        $result = new ResponseData();
        if($files == null) {
            $result->setStatus("failed");
            $result->setMessage("No files in this category!");
            return $result;
        }
        $i = 0;
        $res = '[';
        foreach ($files as $file){
            if($i==1)
                $res .= ',';
            $res .= '{';
            $res .= '"text":"'.$file->getVersions()->last()->getName();
            $res .= '   <b>v'.$file->getVersions()->last()->getVersion().'.0</b>",';
            $res .= '"id":"'.$file->getId().'",';
            $res .= '"a_attr":{';
            $res .= '"href":"/document/file/download/'.$file->getId().'"';
            $res .= '}';
            if($file->getVersions()->count()>1)
                $res .= ',"children":'.$this->getVersionsAsJson($file);
            $res .= '}';
            $i=1;
        }
        $res .= ']';
        $result->setStatus("success");
        $result->setMessage("Files loaded!");
        $result->setData($res);
        return $result;
    }

    private function getVersionsAsJson(File $file){
        $version = $file->getVersions();
        $length = $version->count()-1;
        $res = '[';
        for($i = $length;$i>=0;$i--) {
            if($i!=$length)
                $res .= ',';
            $res .= '{';
            $res .= '"text":"'.$version[$i]->getName();
            $res .= ' v'.$version[$i]->getVersion().'.0';
            $res .= ' <b>UPLOADED:</b> '.$version[$i]->getUploaded()->format("Y.m.d H:i:s");
            $res .= ' <b>USER:</b> '.$version[$i]->getUser()->getName();
            $res .= '",';
            $res .= '"id":"v'.$version[$i]->getId().'",';
            $res .= '"a_attr":{';
                $res .= '"href":"/document/file/download/'.$file->getId().'/version/'.$version[$i]->getId().'"';
                $res .= '}';
            $res .= '}';
        }
        $res .= ']';
        return $res;
    }

    private $entityManager;
    public function saveFileInDatabase(array $data){
        $this->entityManager = $this->getEntityManager();
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(array('user'=>$data['user'],'id'=>$data['categoryId']));
        $files = $category->getFiles();

        if($files->isEmpty())
            return $this->saveNewFile($category, $data);

        foreach ($files as $file){
            if($file->getFilename() == $data['file']['name']){
                return $category->getId().'_'.$this->saveVersionOfFile($file,$data);
            }
        }
        return $this->saveNewFile($category, $data);
    }

    private function saveNewFile(Category $category, array $data){
        $file = new File();
        $file->setFilename($data['file']['name']);

        $version = new Version();
        $version->setName($data['fileName']);
        $version->setUser($data['user']);
        $version->setUploaded(new \DateTime());
        $version->setVersion(1);

        $file->setVersions($version);
        $category->setFiles($file);

        $this->entityManager->persist($version);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $category->getId().'_'.$file->getId().'_'.$version->getId();
    }

    private function saveVersionOfFile(File $file, array $data){
        $version = new Version();
        $version->setName($data['fileName']);
        $version->setUser($data['user']);
        $version->setUploaded(new \DateTime());
        $version->setVersion($file->getVersions()->last()->getVersion()+1);
        $file->setVersions($version);
        $this->entityManager->persist($version);
        $this->entityManager->flush();
        return $file->getId().'_'.$version->getId();
    }

    public function getFile($fileId, $versionId){
        $entityManager = $this->getEntityManager();
        $result = new ResponseData();
        $file = $entityManager->find(File::class,$fileId);

        if($file == null) {
            $result->setFailMessage('File not found!');
            return $result;
        }
        if(!$file->getCategory()->getPermission()->getDownload()) {
            $result->setFailMessage('No download permission!');
            return $result;
        }
        $fileName = $file->getFilename();
        $extension = explode('.',$fileName);
        $extension = $extension[sizeof($extension)-1];
        if($versionId==0) {
            $versionName = $file->getCategory()->getId() . '_' . $file->getId() . '_' . $file->getVersions()->last()->getId();
            return ['fileName' => $fileName, 'versionName' =>$versionName.'.'.$extension];
        }

        foreach ($file->getVersions() as $version) {
            if($version->getId()==$versionId)
                $versionName = $file->getCategory()->getId() . '_' . $file->getId() . '_' . $version->getId();
                return ['fileName'=>$fileName,'versionName'=>$versionName.'.'.$extension];
        }

        $result->setFailMessage('Version not found!');
        return $result;
    }
}