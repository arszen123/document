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
 * @ORM\Entity
 */
class File
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $visibleName;
    /**
     * @ORM\Column(type="string")
     */
    private $filename;
    /**
     * TODO Not needed
     * @ORM\Column(type="integer")
     */
    private $curentVersion;
    /**
     * @ORM\ManyToMany(targetEntity="Version")
     */
    private $versions;

    public function __construct()
    {
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getVisibleName()
    {
        return $this->visibleName;
    }

    /**
     * @param mixed $visibleName
     */
    public function setVisibleName($visibleName)
    {
        $this->visibleName = $visibleName;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getCurentVersion()
    {
        return $this->curentVersion;
    }

    /**
     * @param mixed $curentVersion
     */
    public function setCurentVersion($curentVersion)
    {
        $this->curentVersion = $curentVersion;
    }

    /**
     * @return mixed
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param mixed $versions
     */
    public function setVersions($versions)
    {
        $this->versions[] = $versions;
    }
}