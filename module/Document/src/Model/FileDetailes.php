<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/16/18
 * Time: 1:07 PM
 */

namespace Document\Model;


use Document\Entity\File;
use Document\Entity\Version;
use Zend\Form\Element\DateTime;

class FileDetailes
{

    private $file;
    private $version;
    public function __construct(File $file=null,Version $version=null)
    {
        $this->file = $file;
        $this->version = $version;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * @param Version $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function getDetailesAsJosnObject(){
        $res = '{';
        $res .= '"visibleName":"'.$this->version->getName().'"';
        $res .= ',"filename":"'.$this->file->getFilename().'"';
        $res .= ',"version": "v'.$this->version->getVersion().'.0"';
        $res .= ',"uploaded":"'.$this->version->getUploaded()->format('Y.m.d H:i:s').'"';
        $res .= ',"user":"'.$this->version->getUser()->getName().'"';
        $res .= '}';
        return $res;
    }

    public function getFileAsJsTree(){
        $res = '{';
        $res .= '"text":"'.$this->file->getVersions()->last()->getName();
        $res .= '   <b>v'.$this->file->getVersions()->last()->getVersion().'.0</b>",';
        $res .= '"id":"'.$this->file->getId().'",';
        $res .= '"a_attr":{';
        $res .= '"href":"/document/file/download/'.$this->file->getId().'"';
        $res .= '}';
        if($this->file->getVersions()->count()>1)
            $res .= ',"children":'.$this->getVersionsAsJson();
        $res .= '}';
        return $res;
    }

    private function getVersionsAsJson(){
        $version = $this->file->getVersions();
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
            $res .= '"href":"/document/file/download/'.$this->file->getId().'/version/'.$version[$i]->getId().'"';
            $res .= '}';
            $res .= '}';
        }
        $res .= ']';
        return $res;
    }
}