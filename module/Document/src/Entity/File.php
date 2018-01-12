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
     * @ORM\OneToMany(targetEntity="Version", mappedBy="file")
     */
    private $versions;
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="files")
     */
    private $category;

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
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param Version $version
     */
    public function setVersions(Version $version)
    {
        $version->setFile($this);
        $this->versions[] = $version;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category){
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

}