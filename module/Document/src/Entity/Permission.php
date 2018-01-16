<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/9/18
 * Time: 10:44 AM
 */

namespace Document\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Document\Model\PermissionRepository")
 */
class Permission
{

    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="boolean")
     */
    private $download;
    /**
     * @ORM\Column(type="boolean")
     */
    private $upload;

    public function getId()
    {
        return $this->id;
    }

    public function getDownload()
    {
        return $this->download;
    }


    public function setDownload($download)
    {
        $this->download = $download;
    }


    public function getUpload()
    {
        return $this->upload;
    }


    public function setUpload($upload)
    {
        $this->upload = $upload;
    }
}