<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/13/18
 * Time: 3:21 PM
 */

namespace Document\Form;


use Zend\Form\Element\File as FormFile;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormText;

class UploadFileForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('uploadFileForm');

        $this->add([
            'name' => 'fileName',
            'type' => 'text',
            'required' => true,
            'options' => [
                'label' => 'File name',
            ],
            'attributes' => [
                'placeholder' => 'File name',
                'id' => 'fileName',
                'class'=>'form-control'
            ]
        ]);
        $this->add([
            'name' => 'file',
            'type' => FormFile::class,
            'required' => true,
            'options' => [
                'label' => 'File',
            ],
            'attributes' => [
                'placeholder' => 'File to upload',
                'id' => 'fileToUpload',
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Upload',
                'id'    => 'submitbutton',
                'class' => 'btn btn-default'
            ],
        ]);

    }
}