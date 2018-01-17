<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/13/18
 * Time: 3:25 PM
 */

namespace Document\Model;


use Zend\Filter\File\RenameUpload;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class UploadFile implements InputFilterAwareInterface
{

    private $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        if($this->inputFilter != null)
            return $this->inputFilter;
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'fileName',
            'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ],
                    ],
                ],
        ]);

        $fileInput = new FileInput('file');
        $fileInput->setRequired(true);
        $fileInput->getValidatorChain()->attach(new \Zend\Validator\File\UploadFile());
        $inputFilter->add($fileInput);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }

    public function saveFile($file,$fileName){
        $fileExtension = explode('.',$file['name']);
        $file['name'] = $fileName.'.'.$fileExtension[sizeof($fileExtension)-1];

        $upload = new RenameUpload(__DIR__.'/../assets/files/');
        $upload->setUseUploadName(true);
        $upload->filter($file);
    }
}